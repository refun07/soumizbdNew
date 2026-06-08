<?php

namespace App\Services;

use App\Models\Exchange;
use App\Models\ExchangeItem;
use App\Models\Order;
use App\Models\OrderGroup;
use App\Models\OrderItem;
use App\Models\OrderUpdate;
use App\Models\Product;
use App\Models\ProductVariation;
use App\Models\ProductVariationStock;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ExchangeService
{
    public function index($request)
    {
        $searchKey = null;
        $limit = $request->limit ?? perPage();

        $exchanges = Exchange::forShop()
            ->with('originalOrder', 'exchangeOrder', 'customer', 'items.originalVariation.product', 'items.exchangeVariation.product')
            ->latest();

        if ($request->search != null) {
            $searchKey = $request->search;
            $exchanges = $exchanges->where(function ($q) use ($searchKey) {
                $q->whereHas('customer', function ($subQ) use ($searchKey) {
                    $subQ->where('name', 'like', '%' . $searchKey . '%');
                })->orWhereHas('originalOrder', function ($subQ) use ($searchKey) {
                    $subQ->where('order_code', 'like', '%' . $searchKey . '%');
                });
            });
        }

        if ($request->status != null) {
            $exchanges = $exchanges->where('status', $request->status);
        }

        $exchanges = $exchanges->paginate($limit);

        return [
            'status' => 200,
            'message' => '',
            'result' => [
                'exchanges' => $exchanges,
                'searchKey' => $searchKey,
                'route' => routePrefix(),
            ],
        ];
    }

    public function create($request)
    {
        $searchKey = null;
        $order = null;

        if ($request->search != null) {
            $searchKey = $request->search;
            $order = Order::where('order_code', 'like', '%' . $searchKey . '%')
                ->with('orderItems.productVariation.product', 'orderItems.productVariation.combinations.variationValue', 'user')
                ->first();
        }

        return [
            'status' => 200,
            'message' => '',
            'result' => [
                'searchKey' => $searchKey,
                'order' => $order,
                'route' => routePrefix(),
            ],
        ];
    }

    public function store(array $data)
    {
        DB::beginTransaction();

        try {
            $order = Order::with('orderItems.product', 'orderItems.productVariation.product')->findOrFail($data['order_id']);

            $selectedItems = array_filter($data['items'] ?? [], function ($item) {
                return isset($item['original_order_item_id']);
            });

            $exchangeItems = array_filter($data['exchange_items'] ?? [], function ($item) {
                return isset($item['exchange_product_variation_id']);
            });

            if (empty($selectedItems)) {
                throw ValidationException::withMessages(['items' => 'At least one item must be selected for exchange.']);
            }

            if (empty($exchangeItems)) {
                throw ValidationException::withMessages(['exchange_items' => 'At least one product must be selected to receive.']);
            }

            $totalOriginalPrice = 0;
            $totalExchangePrice = 0;
            $exchangeItemsData = [];

            foreach ($selectedItems as $item) {
                $orderItem = $order->orderItems->firstWhere('id', $item['original_order_item_id']);
                if (!$orderItem) {
                    throw ValidationException::withMessages(['items' => 'Invalid order item selected.']);
                }

                $originalVariation = $orderItem->productVariation;
                $originalProduct = $orderItem->product ?? $originalVariation->product;
                if (!$originalProduct) {
                    throw ValidationException::withMessages(['items' => 'Original product not found for order item ID ' . $orderItem->id]);
                }

                $originalQty = $item['quantity'];
                $originalPrice = variationDiscountedPrice($originalProduct, $originalVariation, false) * $originalQty;
                $totalOriginalPrice += $originalPrice;
            }

            foreach ($exchangeItems as $item) {
                $exchangeVariation = ProductVariation::with('product')->findOrFail($item['exchange_product_variation_id']);
                $exchangeProduct = $exchangeVariation->product;

                if (!$exchangeProduct) {
                    throw ValidationException::withMessages(['exchange_items' => 'Exchange product not found for variation ID ' . $item['exchange_product_variation_id']]);
                }

                $exchangeQty = $item['exchange_quantity'] ?? 1;
                $exchangePrice = variationDiscountedPrice($exchangeProduct, $exchangeVariation, false) * $exchangeQty;
                $totalExchangePrice += $exchangePrice;
            }

            $shippingCharge = $data['shipping_charge'] ?? 0;
            $totalPriceDifference = $totalExchangePrice - ($totalOriginalPrice + $shippingCharge);

            $exchange = Exchange::create([
                'original_order_id' => $order->id,
                'shop_id' => shop()->id ?? $order->shop_id,
                'customer_id' => $order->user_id,
                'status' => 'pending',
                'total_original_price' => $totalOriginalPrice,
                'total_exchange_price' => $totalExchangePrice,
                'price_difference' => $totalPriceDifference,
                'shipping_charge' => $shippingCharge,
                'requested_by' => $data['requested_by'] ?? 0,
                'created_by' => userId(),
            ]);

            $maxItems = max(count($selectedItems), count($exchangeItems));
            for ($i = 0; $i < $maxItems; $i++) {
                $selectedItem = $selectedItems[$i] ?? $selectedItems[count($selectedItems) - 1];
                $exchangeItem = $exchangeItems[$i] ?? $exchangeItems[count($exchangeItems) - 1];

                $orderItem = $order->orderItems->firstWhere('id', $selectedItem['original_order_item_id']);
                $originalVariation = $orderItem->productVariation;
                $originalProduct = $orderItem->product ?? $originalVariation->product;

                $exchangeVariation = ProductVariation::with('product')->findOrFail($exchangeItem['exchange_product_variation_id']);
                $exchangeProduct = $exchangeVariation->product;

                $originalQty = $selectedItem['quantity'];
                $exchangeQty = $exchangeItem['exchange_quantity'] ?? $originalQty;

                $originalUnitPrice = variationDiscountedPrice($originalProduct, $originalVariation, false);
                $exchangeUnitPrice = variationDiscountedPrice($exchangeProduct, $exchangeVariation, false);
                $originalTotalPrice = $originalUnitPrice * $originalQty;
                $exchangeTotalPrice = $exchangeUnitPrice * $exchangeQty;
                $priceDiff = $exchangeTotalPrice - $originalTotalPrice;

                $exchangeItemsData[] = [
                    'original_product_variation_id' => $originalVariation->id,
                    'exchange_product_variation_id' => $exchangeVariation->id,
                    'original_order_item_id' => $orderItem->id,
                    'quantity' => $exchangeQty,
                    'original_quantity' => $originalQty,
                    'original_unit_price' => $originalUnitPrice,
                    'exchange_unit_price' => $exchangeUnitPrice,
                    'price_difference' => $priceDiff,
                    'reason' => $selectedItem['reason'] ?? null,
                    'note' => $selectedItem['note'] ?? null,
                    'status' => 'pending',
                    'media_path' => $selectedItem['media_path'] ?? null,
                ];
            }

            foreach ($exchangeItemsData as $itemData) {
                $itemData['exchange_id'] = $exchange->id;
                ExchangeItem::create($itemData);
            }

            DB::commit();

            return [
                'status' => 200,
                'message' => translate('Exchange request has been created successfully'),
                'result' => ['exchange' => $exchange],
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function process($id)
    {
        DB::beginTransaction();

        try {
            $exchange = Exchange::with('items.originalVariation.product', 'items.exchangeVariation.product', 'originalOrder')->findOrFail($id);

            if ($exchange->status !== 'pending') {
                throw ValidationException::withMessages(['exchange' => 'Only pending exchanges can be processed.']);
            }

            $this->validateExchangeStock($exchange);

            $originalOrder = $exchange->originalOrder;

            $orderGroup = new OrderGroup;
            $orderGroup->user_id = $originalOrder->user_id;
            $orderGroup->name = $originalOrder->user?->name;
            $orderGroup->email = $originalOrder->user?->email;
            $orderGroup->phone = $originalOrder->user?->phone;
            $orderGroup->save();

            $order = new Order;
            $order->user_id = $originalOrder->user_id;
            $order->order_group_id = $orderGroup->id;
            $order->shop_id = $originalOrder->shop_id;
            $order->warehouse_id = $originalOrder->warehouse_id;
            $order->delivery_status = 'order_placed';
            $order->is_exchange_order = true;
            $order->original_order_id = $originalOrder->id;
            $order->agent_id = $originalOrder->agent_id;
            $order->created_by = userId();
            $order->save();

            $subtotal = 0;
            $totalTax = 0;
            $totalDiscount = 0;

            foreach ($exchange->items as $exchangeItem) {
                $exchangeVariation = $exchangeItem->exchangeVariation;
                $exchangeProduct = $exchangeVariation->product;

                $itemTotalPriceWithoutTax = variationPrice($exchangeProduct, $exchangeVariation, false) * $exchangeItem->quantity;
                $itemTotalTax = (variationPrice($exchangeProduct, $exchangeVariation, true) * $exchangeItem->quantity) - $itemTotalPriceWithoutTax;
                $itemTotalDiscount = $itemTotalPriceWithoutTax - (variationDiscountedPrice($exchangeProduct, $exchangeVariation, false) * $exchangeItem->quantity);
                $itemTotalPrice = $itemTotalPriceWithoutTax + $itemTotalTax - $itemTotalDiscount;

                $orderItem = new OrderItem;
                $orderItem->order_id = $order->id;
                $orderItem->product_variation_id = $exchangeVariation->id;
                $orderItem->qty = $exchangeItem->quantity;
                $orderItem->unit_price = variationPrice($exchangeProduct, $exchangeVariation, false);
                $orderItem->total_tax = $itemTotalTax;
                $orderItem->total_discount = $itemTotalDiscount;
                $orderItem->total_price = $itemTotalPrice;
                $orderItem->unit_cost_price = getAvgPurchasePrice($exchangeVariation->id);
                $orderItem->total_cost = $orderItem->unit_cost_price * $orderItem->qty;
                $orderItem->save();

                $exchangeItem->exchange_order_item_id = $orderItem->id;
                $exchangeItem->status = 'processing';
                $exchangeItem->save();

                $subtotal += $itemTotalPriceWithoutTax - $itemTotalDiscount;
                $totalTax += $itemTotalTax;
                $totalDiscount += $itemTotalDiscount;

                $this->returnOriginalStock($exchangeItem, $originalOrder);
                $this->deductExchangeStock($exchangeItem, $originalOrder);
            }

            $order->amount = $subtotal;
            $order->tax_amount = $totalTax;
            $order->shipping_charge_amount = $exchange->shipping_charge;
            $order->discount_amount = $totalDiscount;
            $order->coupon_discount_amount = 0;
            $order->total_amount = $subtotal + $totalTax + $exchange->shipping_charge - $totalDiscount;

            $priceDiff = $exchange->price_difference;

            if ($priceDiff > 0) {
                $order->payment_status = 'unpaid';
                $order->save();

                $transaction = new Transaction;
                $transaction->for = 'exchange';
                $transaction->amount = $subtotal;
                $transaction->tax_amount = $totalTax;
                $transaction->shipping_charge_amount = $exchange->shipping_charge;
                $transaction->discount_amount = $totalDiscount;
                $transaction->coupon_discount_amount = 0;
                $transaction->total_amount = $order->total_amount;
                $transaction->status = 'unpaid';
                $transaction->payment_method = 'manual';
                $transaction->created_by = userId();
                $transaction->save();
            } elseif ($priceDiff < 0) {
                $order->payment_status = 'paid';
                $order->save();

                $transaction = new Transaction;
                $transaction->for = 'exchange';
                $transaction->amount = $subtotal;
                $transaction->tax_amount = $totalTax;
                $transaction->shipping_charge_amount = $exchange->shipping_charge;
                $transaction->discount_amount = $totalDiscount;
                $transaction->coupon_discount_amount = 0;
                $transaction->total_amount = 0;
                $transaction->status = 'paid';
                $transaction->payment_method = 'manual';
                $transaction->created_by = userId();
                $transaction->save();

                $exchange->refund_amount = abs($priceDiff);
                $exchange->save();
            } else {
                $order->payment_status = 'paid';
                $order->save();

                $transaction = new Transaction;
                $transaction->for = 'exchange';
                $transaction->amount = $subtotal;
                $transaction->tax_amount = $totalTax;
                $transaction->shipping_charge_amount = $exchange->shipping_charge;
                $transaction->discount_amount = $totalDiscount;
                $transaction->coupon_discount_amount = 0;
                $transaction->total_amount = 0;
                $transaction->status = 'paid';
                $transaction->payment_method = 'manual';
                $transaction->created_by = userId();
                $transaction->save();
            }

            $orderGroup->transaction_id = $transaction->id;
            $orderGroup->save();

            OrderUpdate::create([
                'order_id' => $order->id,
                'user_id' => userId(),
                'status' => 'Order Placed',
                'type' => 'default',
                'note' => 'Exchange order created',
            ]);

            $exchange->exchange_order_id = $order->id;
            $exchange->status = 'processing';
            $exchange->save();

            DB::commit();

            return [
                'status' => 200,
                'message' => translate('Exchange has been processed successfully'),
                'result' => ['exchange' => $exchange],
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function cancel($id)
    {
        $exchange = Exchange::findOrFail($id);

        if ($exchange->status !== 'pending') {
            throw ValidationException::withMessages(['exchange' => 'Only pending exchanges can be cancelled.']);
        }

        $exchange->status = 'cancelled';
        $exchange->save();

        $exchange->items()->update(['status' => 'cancelled']);

        return [
            'status' => 200,
            'message' => translate('Exchange has been cancelled'),
            'result' => [],
        ];
    }

    public function getById($id)
    {
        return Exchange::with('originalOrder', 'exchangeOrder', 'customer', 'items.originalVariation.product', 'items.exchangeVariation.product', 'items.originalOrderItem')
            ->findOrFail($id);
    }

    public function searchProducts($query)
    {
        $products = Product::has('variations')
            ->where('name', 'like', '%' . $query . '%')
            ->orWhereHas('variations', function ($q) use ($query) {
                $q->where('sku', 'like', '%' . $query . '%');
            })
            ->when(auth()->user()->user_type === 'seller', function ($q) {
                $q->shop();
            })
            ->with('variations.combinations.variationValue')
            ->paginate(20);

        return $products;
    }

    public function getProductVariations($productId)
    {
        $product = Product::with('variations.combinations.variation', 'variations.combinations.variationValue')->find($productId);

        if (!$product) {
            return [];
        }

        $variations = $product->variations;

        if ($variations->isEmpty()) {
            return [];
        }

        return $variations->map(function ($variation) {
            $variationValues = $variation->combinations->map(function ($combination) {
                return $combination->variationValue->name ?? '';
            })->implode(' / ');

            return [
                'id' => $variation->id,
                'sku' => $variation->sku,
                'price' => $variation->price,
                'label' => ($variationValues ? $variationValues . ' - ' : '') . ($variation->sku ? 'SKU: ' . $variation->sku : 'Variation #' . $variation->id) . ' - ' . formatPrice($variation->price),
            ];
        });
    }

    private function validateExchangeItems(array $selectedItems, Order $order): void
    {
        foreach ($selectedItems as $item) {
            $orderItem = $order->orderItems->firstWhere('id', $item['original_order_item_id']);
            if (!$orderItem) {
                throw ValidationException::withMessages(['items' => 'Invalid order item selected.']);
            }

            if ($item['quantity'] > $orderItem->qty) {
                throw ValidationException::withMessages(['items' => 'Exchange quantity cannot exceed original order quantity.']);
            }

            $exchangeVariation = ProductVariation::find($item['exchange_product_variation_id']);
            if (!$exchangeVariation) {
                throw ValidationException::withMessages(['items' => 'Invalid exchange product variation selected.']);
            }
        }
    }

    private function validateExchangeStock(Exchange $exchange): void
    {
        if (getSetting('allowBackorder') == '1') {
            return;
        }

        $order = $exchange->originalOrder;
        $warehouseId = $order->warehouse_id ?: null;

        foreach ($exchange->items as $item) {
            $exchangeVariation = $item->exchangeVariation;

            if (!$exchangeVariation->hasAvailableStock($warehouseId)) {
                $productName = $exchangeVariation->product->name ?? 'Unknown product';
                throw ValidationException::withMessages([
                    'items' => "Insufficient stock for exchange item \"{$productName}\".",
                ]);
            }

            $availableStock = $exchangeVariation->getStockQuantity($warehouseId);
            if ($availableStock < $item->quantity) {
                $productName = $exchangeVariation->product->name ?? 'Unknown product';
                throw ValidationException::withMessages([
                    'items' => "Insufficient stock for exchange item \"{$productName}\". Available: {$availableStock}, Requested: {$item->quantity}",
                ]);
            }
        }
    }

    private function returnOriginalStock(ExchangeItem $exchangeItem, Order $originalOrder): void
    {
        $warehouseId = $originalOrder->warehouse_id ?: null;

        $stock = new ProductVariationStock;
        $stock->product_variation_id = $exchangeItem->original_product_variation_id;
        $stock->warehouse_id = $warehouseId;
        $stock->stock_qty = $exchangeItem->original_quantity;
        $stock->save();
    }

    private function deductExchangeStock(ExchangeItem $exchangeItem, Order $originalOrder): void
    {
        $warehouseId = $originalOrder->warehouse_id ?: null;
        $allowBackorder = getSetting('allowBackorder') == '1';

        $query = ProductVariationStock::where('product_variation_id', $exchangeItem->exchange_product_variation_id)
            ->where('stock_qty', '>', 0);

        if ($warehouseId) {
            $query->where('warehouse_id', $warehouseId);
        }

        $stocks = $query->orderByDesc('id')->lockForUpdate()->get();

        $remaining = $exchangeItem->quantity;

        foreach ($stocks as $stock) {
            if ($remaining <= 0) break;

            $take = min($stock->stock_qty, $remaining);
            $stock->stock_qty -= $take;
            $stock->save();
            $remaining -= $take;
        }

        if ($remaining > 0) {
            if ($allowBackorder) {
                $deficitStock = new ProductVariationStock;
                $deficitStock->product_variation_id = $exchangeItem->exchange_product_variation_id;
                $deficitStock->warehouse_id = $warehouseId;
                $deficitStock->stock_qty = -$remaining;
                $deficitStock->save();

                $remaining = 0;
            } else {
                throw ValidationException::withMessages([
                    'items' => "Insufficient stock to fulfill exchange. Could not deduct {$remaining} unit(s).",
                ]);
            }
        }
    }
}
