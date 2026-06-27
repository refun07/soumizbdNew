<?php

namespace App\Http\Controllers\Backend\Admin\Orders;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\Request;
use ZipArchive;

class OrderController extends Controller
{
    protected $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;

        $this->middleware(['permission:view_orders|manage_assigned_orders|manage_orders'])->only(['index', 'downloadInvoice', 'returnDetails', 'getOrderDetails']);
        $this->middleware(['permission:manage_assigned_orders|manage_orders'])->only(['show', 'updateOrderTracking', 'storeOrderUpdates', 'deleteOrderUpdate', 'updatePaymentStatus', 'updateDeliveryStatus', 'updateOrderAddress', 'edit', 'updateOrder', 'searchProducts', 'downloadChallan', 'downloadMultipleInvoice', 'downloadSinglePageInvoice', 'orderPayment']);
    }

    private function authorizeOrderManagement(Order $order)
    {
        $user = auth()->user();
        if ($user->can('manage_orders')) {
            return;
        }
        if ($user->can('manage_assigned_orders') && $order->agent_id === $user->id) {
            return;
        }
        abort(403, 'You are not authorized to manage this order.');
    }

    private function authorizeOrderManagementById($orderId)
    {
        $order = Order::findOrFail($orderId);
        $this->authorizeOrderManagement($order);
    }

    // get all resources
    public function index(Request $request)
    {

        $response = $this->orderService->index($request);
        $symbolAlignMent = [
            'symbol_first',
            'amount_first',
            'symbol_space',
            'amount_space',
        ];

        $settings = [
            // currency settings
            'currency' => [
                'code' => getSetting('currencyCode') ?? 'usd',
                'symbol' => [
                    'position' => getSetting('currencySymbolAlignment') ? $symbolAlignMent[getSetting('currencySymbolAlignment') ? getSetting('currencySymbolAlignment') - 1 : 0] : 'symbol_first',

                    'show' => getSetting('currencySymbol') ?? '$',
                ],
                'thousandSeparator' => getSetting('thousandSeparator') ?? null,
                'numOfDecimals' => getSetting('numOfDecimals') ?? 0,
                'decimalSeparator' => getSetting('decimalSeparator') ?? '.',
            ],
            'countries' => Country::where('is_active', 1)->get(),
        ];

        // $view = view('backend.admin.pos.index', compact('settings'));
        if ($response['status'] == 200) {
            $orders = $response['result'];

            return view('backend.admin.orders.index', compact('settings', 'orders'));
        }
        flash($response['message'] ?? translate('Something went wrong'))->error();

        return redirect()->route('admin.dashboard');
    }

    // return details page
    public function returnDetails(Request $request)
    {
        $symbolAlignMent = [
            'symbol_first',
            'amount_first',
            'symbol_space',
            'amount_space',
        ];

        $settings = [
            // currency settings
            'currency' => [
                'code' => getSetting('currencyCode') ?? 'usd',
                'symbol' => [
                    'position' => getSetting('currencySymbolAlignment') ? $symbolAlignMent[getSetting('currencySymbolAlignment') ? getSetting('currencySymbolAlignment') - 1 : 0] : 'symbol_first',

                    'show' => getSetting('currencySymbol') ?? '$',
                ],
                'thousandSeparator' => getSetting('thousandSeparator') ?? null,
                'numOfDecimals' => getSetting('numOfDecimals') ?? 0,
                'decimalSeparator' => getSetting('decimalSeparator') ?? '.',
            ],
            'countries' => Country::where('is_active', 1)->get(),
        ];

        return view('backend.admin.return-details.index', compact('settings'));
    }

    // show orders
    public function show($code)
    {
        $order = Order::whereOrderCode($code)->firstOrFail();
        $this->authorizeOrderManagement($order);

        $response = $this->orderService->show($code);
        if ($response['status'] == 200) {
            return view('backend.admin.orders.show', $response['result']);
        }

        flash($response['message'] ?? translate('Something went wrong'))->error();

        return redirect()->route('admin.orders.index');
    }

    // edit order
    public function edit($code)
    {
        $order = Order::whereOrderCode($code)->firstOrFail();
        $this->authorizeOrderManagement($order);

        $response = $this->orderService->show($code);
        if ($response['status'] == 200) {
            return view('backend.admin.orders.edit', $response['result']);
        }

        flash($response['message'] ?? translate('Something went wrong'))->error();

        return redirect()->route('admin.orders.index');
    }

    // update order
    public function updateOrder(Request $request, $code)
    {
        $order = Order::whereOrderCode($code)->firstOrFail();
        $this->authorizeOrderManagement($order);

        $response = $this->orderService->updateOrder($request, $code);
        if ($response['status'] == 200) {
            flash($response['message'])->success();

            return redirect()->route('admin.orders.show', $code);
        }

        flash($response['message'] ?? translate('Something went wrong'))->error();

        return back();
    }

    // search products for adding to order
    public function searchProducts(Request $request)
    {
        $query = $request->get('q', '');

        $products = \App\Models\Product::with(['variations.productVariationStocks'])
            ->where('name', 'like', "%{$query}%")
            ->orWhere('slug', 'like', "%{$query}%")
            ->limit(20)
            ->get();

        $result = $products->map(function ($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'image' => uploadedAsset($product->thumbnail_image),
                'variations' => $product->variations->map(function ($variation) use ($product) {
                    $stock = $variation->productVariationStocks->sum('stock_qty');

                    return [
                        'id' => $variation->id,
                        'name' => $variation->code ? generateVariationName($variation->code) : null,
                        'price' => $variation->price ?? $product->base_price,
                        'stock' => $stock,
                    ];
                }),
            ];
        });

        return response()->json(['products' => $result]);
    }

    // download invoice
    public function downloadInvoice($id)
    {
        // return view('backend.admin.orders.invoice');
        return $this->orderService->downloadInvoice($id);
    }

    // download challan
    public function downloadChallan($id)
    {
        $this->authorizeOrderManagementById($id);

        return $this->orderService->downloadChallan($id);
    }

    // show public invoice by hash (for QR code scanning)
    public function showPublicInvoice($hash)
    {
        $orderId = decodeInvoiceHash($hash);

        if (! $orderId) {
            abort(404, 'Invalid invoice link');
        }

        $order = Order::with(['orderItems.productVariation.product.productTranslations'])
            ->whereId($orderId)
            ->first();

        if (! $order) {
            abort(404, 'Invoice not found');
        }

        $products = $order->orderItems->map(function ($item) {
            $product = $item->productVariation->product;
            $translation = $product->productTranslations->first();

            return [
                'name' => $translation ? $translation->name : $product->name,
                'image' => $product->thumbnail_image
                    ? uploadedAsset($product->thumbnail_image)
                    : asset('assets/img/placeholder.jpg'),
            ];
        });

        return view('frontend.invoice-public', [
            'order' => $order,
            'products' => $products,
            'invoiceCode' => getSetting('order_code_prefix').$order->order_code,
        ]);
    }

    public function downloadMultipleInvoice($ids)
    {
        $idArray = explode(',', $ids);
        foreach ($idArray as $id) {
            $this->authorizeOrderManagementById($id);
        }

        $zipFileName = 'invoices_'.now()->timestamp.'.zip';
        $zipPath = storage_path('app/tmp/'.$zipFileName);

        // Ensure temp directory exists
        if (! file_exists(storage_path('app/tmp'))) {
            mkdir(storage_path('app/tmp'), 0755, true);
        }

        $zip = new ZipArchive;
        $zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);
        foreach ($idArray as $id) {
            $order = Order::findOrFail($id);

            $pdf = $this->orderService->downloadInvoice($id, true);

            $filename = 'invoice_'.$order->order_code.'.pdf';
            $tempPdfPath = storage_path('app/tmp/'.$filename);
            $pdf->save($tempPdfPath);

            $zip->addFile($tempPdfPath, $filename);
        }
        $zip->close();

        foreach ($idArray as $id) {
            $order = Order::findOrFail($id);
            $filename = 'invoice_'.$order->order_code.'.pdf';
            $tempPdfPath = storage_path('app/tmp/'.$filename);
            if (file_exists($tempPdfPath)) {
                unlink($tempPdfPath);
            }
        }

        // Return the ZIP file for download

        return response()->download($zipPath, $zipFileName)->deleteFileAfterSend(true);
    }

    public function downloadSinglePageInvoice($ids)
    {
        $idArray = explode(',', $ids);
        foreach ($idArray as $id) {
            $this->authorizeOrderManagementById($id);
        }

        return $this->orderService->downloadSinglePageInvoice($idArray);
    }

    // store or update order tracking
    public function updateOrderTracking(Request $request)
    {
        $this->authorizeOrderManagementById($request->id);

        $response = $this->orderService->updateOrderTracking($request);
        if ($response['status'] == 200) {
            flash($response['message'])->success();

            return back();
        }

        flash($response['message'] ?? translate('Something went wrong'))->error();

        return back();
    }

    // store new order updates
    public function storeOrderUpdates(Request $request)
    {
        $this->authorizeOrderManagementById($request->order_id);

        $response = $this->orderService->storeOrderUpdates($request);
        if ($response['status'] == 200) {
            flash($response['message'])->success();

            return back();
        }

        flash($response['message'] ?? translate('Something went wrong'))->error();

        return back();
    }

    // delete order update
    public function deleteOrderUpdate($id)
    {
        $orderUpdate = \App\Models\OrderUpdate::findOrFail($id);
        $this->authorizeOrderManagement($orderUpdate->order);

        $response = $this->orderService->deleteOrderUpdate($id);
        if ($response['status'] == 200) {
            flash($response['message'])->success();

            return back();
        }
        flash($response['message'] ?? translate('Something went wrong'))->error();

        return back();
    }

    // update payment status
    public function updatePaymentStatus(Request $request)
    {
        $this->authorizeOrderManagementById($request->order_id);

        $response = $this->orderService->updatePaymentStatus($request);
        if ($response['status'] == 200) {
            return true;
        }

        return false;
    }

    // update delivery status
    public function updateDeliveryStatus(Request $request)
    {
        $this->authorizeOrderManagementById($request->order_id);

        $response = $this->orderService->updateDeliveryStatus($request);
        if ($response['status'] == 200) {
            return true;
        }

        return false;
    }

    // update Order Address
    public function updateOrderAddress(Request $request)
    {
        $this->authorizeOrderManagementById($request->order_id);

        $response = $this->orderService->updateOrderAddress($request);
        if ($response['status'] == 200) {
            flash($response['message'])->success();

            return back();
        }
        flash($response['message'] ?? translate('Something went wrong'))->error();

        return back();
    }

    // order payment
    public function orderPayment(Request $request)
    {
        $this->authorizeOrderManagementById($request->order_id);

        $response = $this->orderService->orderPayment($request);
        if ($response['status'] == 200) {
            flash($response['message'])->success();

            return back();
        }
        flash($response['message'] ?? translate('Something went wrong'))->error();

        return back();
    }

    // get order details for React frontend (API)
    public function getOrderDetails($orderId)
    {
        try {
            $order = Order::with([
                'user',
                'orderItems.productVariation.product',
                'orderGroup',
                'latestDeliveryStatusUpdate.creator',
                'latestDeliveryStatusUpdate.user',
                'exchangeAsOriginal.items.originalVariation.product',
                'exchangeAsOriginal.items.exchangeVariation.product',
                'exchangeAsOriginal.exchangeOrder',
                'exchangeAsReplacement.items.originalVariation.product',
                'exchangeAsReplacement.items.exchangeVariation.product',
                'exchangeAsReplacement.originalOrder',
            ])->findOrFail($orderId);

            $orderGroup = $order->orderGroup;

            // Get shipping address from order group
            $shippingAddress = $orderGroup?->shipping_address ?? null;
            $billingAddress = $orderGroup?->billing_address ?? null;

            // Format order data
            $orderData = [
                'id' => $order->id,
                'order_code' => $order->order_code,
                'orderPrefix' => getSetting('orderCodePrefix'),
                // Prefer order-specific snapshot values from order_group over current user profile values.
                'customerName' => $orderGroup?->name ?? $order->user?->name,
                'customerEmail' => $orderGroup?->email ?? $order->user?->email,
                'customerPhone' => $orderGroup?->phone ?? $order->user?->phone,
                'createdDate' => $order->created_at->format('d M, Y'),
                'createdTime' => $order->created_at->format('h:i A'),
                'deliveryStatus' => $order->delivery_status,
                'deliveryStatusToShow' => ucfirst(str_replace('_', ' ', $order->delivery_status)),
                'paymentStatus' => $order->payment_status,
                'paymentType' => $orderGroup?->transaction?->payment_method ?? $order->payment_type,
                'shipping_address' => $shippingAddress,
                'billing_address' => $billingAddress,
                'shipping_city' => null,
                'shipping_country' => null,
                'subtotalAmount' => (float) $order->amount,
                'taxAmount' => (float) $order->tax_amount,
                'shippingChargeAmount' => (float) $order->shipping_charge_amount,
                'discountAmount' => (float) $order->discount_amount,
                'couponDiscountAmount' => (float) $order->coupon_discount_amount,
                'totalAmount' => (float) $order->total_amount,
                'isPosOrder' => $orderGroup?->is_pos_order ?? false,
                'isExchangeOrder' => $order->is_exchange_order,
                'originalOrderId' => $order->original_order_id,
                'tracking_number' => $orderGroup?->tracking_number ?? $order->tracking_number,
                'courier_partner' => $orderGroup?->courier_partner ?? $order->courier_name,
                'courier_delivery_status' => $orderGroup?->delivery_status,
                'courier_response' => $orderGroup?->courier_response,
                'ipAddress' => $order->ip_address,
                'latestDeliveryUpdate' => $order->latestDeliveryStatusUpdate ? [
                    'status' => $order->latestDeliveryStatusUpdate->status,
                    'note' => $order->latestDeliveryStatusUpdate->note,
                    'actorName' => $order->latestDeliveryStatusUpdate->getActorName() ?? 'System',
                    'actorRole' => $order->latestDeliveryStatusUpdate->getActorRole(),
                    'createdDate' => $order->latestDeliveryStatusUpdate->created_at->format('d M, Y'),
                    'createdTime' => $order->latestDeliveryStatusUpdate->created_at->format('h:i A'),
                ] : null,
                'exchangeInfo' => $this->formatExchangeInfo($order),
            ];

            // Format order items
            $orderItems = $order->orderItems->map(function ($item) {
                $variantName = null;
                if ($item->productVariation && $item->productVariation->code) {
                    $variantName = generateVariationName($item->productVariation->code);
                }

                return [
                    'id' => $item->id,
                    'product_name' => $item->productVariation->product->name ?? 'N/A',
                    'variant' => $variantName,
                    'product_image' => $item->productVariation->product->thumbnail_image ? uploadedAsset($item->productVariation->product->thumbnail_image) : null,
                    'quantity' => $item->qty,
                    'unit_price' => (float) $item->unit_price,
                    'total_price' => (float) $item->total_price,
                ];
            });

            return response()->json([
                'success' => true,
                'status' => 200,
                'result' => [
                    'order' => $orderData,
                    'items' => $orderItems,
                ],
            ]);
        } catch (\Exception $e) {
            \Log::error('Error fetching order details: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'status' => 500,
                'message' => 'Something went wrong',
            ], 500);
        }
    }

    private function formatExchangeInfo($order)
    {
        $orderPrefix = getSetting('orderCodePrefix');

        if ($order->is_exchange_order && $order->exchangeAsReplacement) {
            $exchange = $order->exchangeAsReplacement;
            $originalOrder = $exchange->originalOrder;

            return [
                'type' => 'exchange_order',
                'exchangeId' => $exchange->id,
                'status' => $exchange->status,
                'originalOrderCode' => $originalOrder ? $orderPrefix.$originalOrder->order_code : null,
                'originalOrderRawCode' => $originalOrder?->order_code,
                'originalOrderId' => $originalOrder?->id,
                'priceDifference' => (float) $exchange->price_difference,
                'refundAmount' => $exchange->refund_amount ? (float) $exchange->refund_amount : null,
                'shippingCharge' => (float) $exchange->shipping_charge,
                'totalOriginalPrice' => (float) $exchange->total_original_price,
                'totalExchangePrice' => (float) $exchange->total_exchange_price,
                'items' => $exchange->items->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'originalProductName' => $item->originalVariation?->product?->name ?? 'N/A',
                        'originalVariant' => $item->originalVariation?->code ? generateVariationName($item->originalVariation->code) : null,
                        'exchangeProductName' => $item->exchangeVariation?->product?->name ?? 'N/A',
                        'exchangeVariant' => $item->exchangeVariation?->code ? generateVariationName($item->exchangeVariation->code) : null,
                        'quantity' => $item->quantity,
                        'originalQuantity' => $item->original_quantity,
                        'originalUnitPrice' => (float) $item->original_unit_price,
                        'exchangeUnitPrice' => (float) $item->exchange_unit_price,
                        'priceDifference' => (float) $item->price_difference,
                        'reason' => $item->reason,
                        'status' => $item->status,
                    ];
                }),
            ];
        }

        if ($order->exchangeAsOriginal) {
            $exchange = $order->exchangeAsOriginal;
            $exchangeOrder = $exchange->exchangeOrder;

            return [
                'type' => 'original_order',
                'exchangeId' => $exchange->id,
                'status' => $exchange->status,
                'exchangeOrderCode' => $exchangeOrder ? $orderPrefix.$exchangeOrder->order_code : null,
                'exchangeOrderRawCode' => $exchangeOrder?->order_code,
                'exchangeOrderId' => $exchangeOrder?->id,
                'priceDifference' => (float) $exchange->price_difference,
                'refundAmount' => $exchange->refund_amount ? (float) $exchange->refund_amount : null,
                'shippingCharge' => (float) $exchange->shipping_charge,
                'totalOriginalPrice' => (float) $exchange->total_original_price,
                'totalExchangePrice' => (float) $exchange->total_exchange_price,
                'items' => $exchange->items->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'originalProductName' => $item->originalVariation?->product?->name ?? 'N/A',
                        'originalVariant' => $item->originalVariation?->code ? generateVariationName($item->originalVariation->code) : null,
                        'exchangeProductName' => $item->exchangeVariation?->product?->name ?? 'N/A',
                        'exchangeVariant' => $item->exchangeVariation?->code ? generateVariationName($item->exchangeVariation->code) : null,
                        'quantity' => $item->quantity,
                        'originalQuantity' => $item->original_quantity,
                        'originalUnitPrice' => (float) $item->original_unit_price,
                        'exchangeUnitPrice' => (float) $item->exchange_unit_price,
                        'priceDifference' => (float) $item->price_difference,
                        'reason' => $item->reason,
                        'status' => $item->status,
                    ];
                }),
            ];
        }

        return null;
    }
}
