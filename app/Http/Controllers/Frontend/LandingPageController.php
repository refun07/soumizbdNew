<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Country;
use App\Models\Notification;
use App\Models\Order;
use App\Models\OrderGroup;
use App\Models\OrderItem;
use App\Models\OrderUpdate;
use App\Models\Product;
use App\Models\Shop;
use App\Models\State;
use App\Models\SystemSetting;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Warehouse;
use App\Models\WebsiteLanding;
use App\Notifications\OrderPlacedNotification;
use App\Services\ShippingService;
use App\Services\SmsService;
use App\Traits\CategoryTrait;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Notification as GlobalNotification;

class LandingPageController extends Controller
{
    /**
     * Display the landing page
     */
    public function show($slug)
    {
        $landingPage = WebsiteLanding::where('slug', $slug)
            ->where('status', 'published')
            ->with('sections')
            ->firstOrFail();

        // Get selected products - bypass the problematic array cast and decode manually
        $rawSelectedProducts = $landingPage->getRawOriginal('selected_products');
        $selectedProducts = [];
        if ($rawSelectedProducts) {
            $decoded = json_decode($rawSelectedProducts, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $selectedProducts = $decoded;
            }
        }
        \Log::info('Selected products: ' . print_r($selectedProducts, true));

        // Get the selected products
        $products = collect([]);
        if (!empty($selectedProducts)) {
            $products = \App\Models\Product::whereIn('id', $selectedProducts)
                ->with(['productTranslations', 'variations.productVariationStocks', 'variations.combinations.variationValue.variationValueTranslations'])
                ->get()
                ->map(function ($product) {
                    $firstVariation = $product->variations->first();
                    
                    // Calculate total stock for each variation from all warehouses
                    $variationsWithStock = $product->variations->map(function($v) use ($product) {
                        $totalStock = $v->productVariationStocks->sum('stock_qty');
                        
                        // Build variation name from combinations
                        $variationNames = [];
                        foreach ($v->combinations as $combination) {
                            if ($combination->variationValue) {
                                $variationNames[] = $combination->variationValue->collectTranslation('name');
                            }
                        }
                        $variationName = !empty($variationNames) ? implode(' - ', $variationNames) : 'Variant #' . $v->id;

                        $basePrice = variationPrice($product, $v);
                        $discountedPrice = variationDiscountedPrice($product, $v);
                        $discountPercent = ($basePrice > 0 && $discountedPrice < $basePrice)
                            ? round((($basePrice - $discountedPrice) / $basePrice) * 100)
                            : 0;
                        
                        return [
                            'id' => $v->id,
                            'name' => $variationName,
                            'price' => $discountedPrice,
                            'base_price' => $basePrice,
                            'discount_percent' => $discountPercent,
                            'stock' => $totalStock,
                            'total_stock' => $totalStock,
                        ];
                    });
                    
                    // Filter variations with stock > 0
                    $availableVariations = $variationsWithStock->filter(function($v) {
                        return $v['total_stock'] > 0;
                    })->values()->toArray();
                    
                    $firstBasePrice = $firstVariation ? variationPrice($product, $firstVariation) : 0;
                    $firstDiscountedPrice = $firstVariation ? variationDiscountedPrice($product, $firstVariation) : 0;
                    $firstDiscountPercent = ($firstBasePrice > 0 && $firstDiscountedPrice < $firstBasePrice)
                        ? round((($firstBasePrice - $firstDiscountedPrice) / $firstBasePrice) * 100)
                        : 0;

                    return [
                        'id' => $product->id,
                        'name' => $product->collectTranslation('name'),
                        'sku' => $product->sku,
                        'image' => uploadedAsset($product->thumbnail_image),
                        'price' => $firstDiscountedPrice,
                        'base_price' => $firstBasePrice,
                        'discount_percent' => $firstDiscountPercent,
                        'variation_id' => $firstVariation->id ?? null,
                        'variations' => $availableVariations,
                        'has_variations' => $product->variations->count() > 1,
                        'shop_id' => $product->shop_id,
                        'shop_name' => $product->shop?->name ?? 'Default Seller',
                    ];
                });
        }

        // Get landing page payment method configuration
        $pageConfigs = $landingPage->page_configs ?? [];
        $configuredPaymentMethods = $pageConfigs['payment_methods'] ?? null;

        // Get payment methods
        $paymentMethods = [];
        if (getSetting('cod_activation') == 1) {
            $payment = new SystemSetting;
            $payment->name = translate("Cash On Delivery");
            $payment->value = "cash_on_delivery";
            $paymentMethods[] = $payment;
        }

        if (getSetting('card_activation') == 1) {
            $payment = new SystemSetting;
            $payment->name = translate("Card On Delivery");
            $payment->value = "card_on_delivery";
            $paymentMethods[] = $payment;
        }

        if (getSetting('paypal_activation') == 1) {
            $payment = new SystemSetting;
            $payment->name = translate("Paypal");
            $payment->value = "paypal";
            $paymentMethods[] = $payment;
        }

        if (getSetting('stripe_activation') == 1) {
            $payment = new SystemSetting;
            $payment->name = translate("Stripe");
            $payment->value = "stripe";
            $paymentMethods[] = $payment;
        }

        if (getSetting('sslcommerz_activation') == 1) {
            $payment = new SystemSetting;
            $payment->name = translate("SSLCommerz");
            $payment->value = "sslcommerz";
            $paymentMethods[] = $payment;
        }

        if (getSetting('bkash_activation') == 1) {
            $payment = new SystemSetting;
            $payment->name = translate("bKash");
            $payment->value = "bkash";
            $paymentMethods[] = $payment;
        }

        if (getSetting('flutterwave_activation') == 1) {
            $payment = new SystemSetting;
            $payment->name = translate("Flutterwave");
            $payment->value = "flutterwave";
            $paymentMethods[] = $payment;
        }

        if (getSetting('paytm_activation') == 1) {
            $payment = new SystemSetting;
            $payment->name = translate("Paytm");
            $payment->value = "paytm";
            $paymentMethods[] = $payment;
        }

        if (getSetting('paystack_activation') == 1) {
            $payment = new SystemSetting;
            $payment->name = translate("Paystack");
            $payment->value = "paystack";
            $paymentMethods[] = $payment;
        }

        if (getSetting('coingate_activation') == 1) {
            $payment = new SystemSetting;
            $payment->name = translate("CoinGate");
            $payment->value = "coingate";
            $paymentMethods[] = $payment;
        }

        if (getSetting('iyzico_activation') == 1) {
            $payment = new SystemSetting;
            $payment->name = translate("Iyzico");
            $payment->value = "iyzico";
            $paymentMethods[] = $payment;
        }

        if (getSetting('razorpay_activation') == 1) {
            $payment = new SystemSetting;
            $payment->name = translate("Razorpay");
            $payment->value = "razorpay";
            $paymentMethods[] = $payment;
        }

        if (getSetting('instamojo_activation') == 1) {
            $payment = new SystemSetting;
            $payment->name = translate("Instamojo");
            $payment->value = "instamojo";
            $paymentMethods[] = $payment;
        }

        if (getSetting('aamarpay_activation') == 1) {
            $payment = new SystemSetting;
            $payment->name = translate("Aamarpay");
            $payment->value = "aamarpay";
            $paymentMethods[] = $payment;
        }

        // Filter by landing page configuration if set
        if ($configuredPaymentMethods !== null && is_array($configuredPaymentMethods)) {
            $paymentMethods = array_filter($paymentMethods, function($method) use ($configuredPaymentMethods) {
                return in_array($method->value, $configuredPaymentMethods);
            });
            
            // Re-index array
            $paymentMethods = array_values($paymentMethods);
        }

        // Sort sections by order
        $landingPage->sections = $landingPage->sections->sortBy('order')->filter(function ($section) {
            return $section->is_visible;
        })->values();

        // Get page configs (colors) - already loaded earlier for payment methods
        $themeColor = $pageConfigs['theme_color'] ?? '#1a472a';
        $primaryColor = $pageConfigs['primary_color'] ?? '#E68A3D';

        // Load coupon if configured
        $coupon = null;
        if ($landingPage->coupon_id) {
            $coupon = \App\Models\Coupon::find($landingPage->coupon_id);
        }

        // Facebook Pixel configuration
        $facebookPixelActive = config('app.FACEBOOK_PIXEL_ACTIVATION') == '1';
        $facebookPixelEnabled = $pageConfigs['facebook_pixel_enabled'] ?? false;
        $metaPixelId = config('app.META_PIXEL_ID');

        // Get Bangladesh country ID dynamically (fallback to hardcoded 18 if not found)
        $bangladeshCountry = Country::where('code', 'BD')
            ->orWhere('name', 'Bangladesh')
            ->first();
        $bangladeshCountryId = $bangladeshCountry?->id ?? 18;

        // Prepare general settings for JavaScript
        $generalSettings = [
            'appName' => config('app.name'),
            'appMode' => config('app.app_mode'),
            'demoMode' => config('app.demo_mode'),
            'env' => config('app.env'),
            'allowBackorder' => getSetting('allowBackorder') == '1',
            'customerLoginMethod' => getSetting('customerLoginMethod') ?? 'email',
        ];

        return view('frontend.landing.show', compact(
            'landingPage',
            'products',
            'paymentMethods',
            'themeColor',
            'primaryColor',
            'coupon',
            'facebookPixelActive',
            'facebookPixelEnabled',
            'metaPixelId',
            'bangladeshCountryId',
            'generalSettings'
        ));
    }

    /**
     * Handle landing page order submission
     */
    public function storeOrder(Request $request)
    {
        $request->validate([
            'landing_page_id' => 'required|exists:website_landings,id',
            'items' => 'required|array',
        ]);

        // Get landing page and validate coupon
        $landingPage = WebsiteLanding::find($request->landing_page_id);
        $coupon = null;
        $couponDiscount = 0;

        // Check if customer entered a coupon code
        $enteredCouponCode = $request->input('coupon_code');

        if ($landingPage && $landingPage->coupon_id && $enteredCouponCode) {
            $coupon = \App\Models\Coupon::find($landingPage->coupon_id);

            // Validate coupon is still active and code matches
            if ($coupon && $coupon->is_active) {
                // Check if entered code matches the landing page coupon code
                if (strtoupper($enteredCouponCode) !== strtoupper($coupon->code)) {
                    $coupon = null; // Code doesn't match
                } else {
                    $now = now();
                    if ((!$coupon->start_date || $now >= $coupon->start_date) &&
                        (!$coupon->end_date || $now <= $coupon->end_date)) {
                        // Coupon is valid, will calculate discount later
                    } else {
                        $coupon = null; // Coupon expired
                    }
                }
            } else {
                $coupon = null; // Coupon inactive
            }
        }

        // Items come as form array directly
        $items = $request->items;
        if (!is_array($items) || empty($items)) {
            return back()->withErrors(['items' => 'Please select at least one product'])->withInput();
        }

        // Get customer details from form
        $name = $request->input('customer_details.name');
        $email = $request->input('customer_details.email');
        $phone = $request->input('customer_details.phone');
        $alternativePhone = $request->input('customer_details.alternative_phone');
        $shippingAddress = $request->input('customer_details.shipping_address');
        $stateId = $request->input('customer_details.state_id');
        $cityId = $request->input('customer_details.city_id');
        $paymentMethod = $request->input('customer_details.payment_method');
        $createAccount = $request->input('create_account');
        $password = $request->input('customer_details.password');

        // Validate required fields
        $validatorData = [
            'name' => $name,
            'phone' => $phone,
            'shipping_address' => $shippingAddress,
            'state_id' => $stateId,
            'city_id' => $cityId,
            'payment_method' => $paymentMethod,
        ];
        $validatorRules = [
            'name' => 'required|string',
            'phone' => 'required|string',
            'shipping_address' => 'required|string',
            'state_id' => 'required',
            'city_id' => 'required',
            'payment_method' => 'required|string',
        ];

        // Validate password if create account is checked
        if ($createAccount) {
            $validatorData['password'] = $password;
            $validatorRules['password'] = 'required|string|min:6';
        }

        $validator = \Validator::make($validatorData, $validatorRules);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Validate state/district exists
        $state = State::find($stateId);
        if (!$state) {
            return back()->withErrors(['state_id' => 'Selected district not found'])->withInput();
        }

        // Validate city exists
        $city = City::find($cityId);
        if (!$city) {
            return back()->withErrors(['city_id' => 'Selected city not found'])->withInput();
        }

        // Normalize phone number to local format (01XXXXXXXXX)
        $normalizedPhone = normalizePhoneToLocal($phone);

        // Check login method
        $isPhoneLogin = getSetting('customerLoginMethod') === 'phone';

        // Create or find user by phone
        $user = User::where('phone', $normalizedPhone)->first();
        if (!$user) {
            $userPassword = $createAccount && $password ? $password : '123456';
            $user = User::create([
                'name' => $name,
                'email' => $email ?: null,
                'phone' => $normalizedPhone,
                'password' => Hash::make($userPassword),
                'email_or_otp_verified' => $isPhoneLogin ? 0 : 1,
            ]);

            // For phone login mode with account creation, we should verify phone
            // But for landing page orders, we'll auto-verify to avoid friction
            if ($isPhoneLogin) {
                $user->email_or_otp_verified = 1;
                $user->save();
            }
        }

        // Get all warehouse IDs from database for multi-warehouse stock checking
        $allWarehouseIds = Warehouse::pluck('id')->toArray();

        // Load items with product and variation data
        $orderItems = [];
        foreach ($items as $item) {
            $variation = \App\Models\ProductVariation::find($item['variation_id'] ?? 0);
            if (!$variation) {
                continue;
            }
            $product = $variation->product;
            if (!$product) {
                continue;
            }

            $orderItems[] = [
                'product' => $product,
                'variation' => $variation,
                'quantity' => $item['quantity'] ?? 1,
                'warehouse_id' => null, // Will be determined based on stock availability
            ];
        }

        if (empty($orderItems)) {
            return back()->withErrors(['error' => 'No valid products found in your order'])->withInput();
        }

        // Check stock across all warehouses for each item
        $allowBackorder = getSetting('allowBackorder') == '1';
        foreach ($orderItems as $key => $item) {
            $product = $item['product'];
            $variation = $item['variation'];
            $quantity = $item['quantity'];

            if ($allowBackorder) {
                // Backorder enabled: assign to any available warehouse without stock check
                $availableWarehouseStock = $variation->productVariationStocks()
                    ->whereIn('warehouse_id', $allWarehouseIds)
                    ->first();

                if (!$availableWarehouseStock) {
                    // Assign to first warehouse if no stock record exists
                    $orderItems[$key]['warehouse_id'] = $allWarehouseIds[0] ?? null;
                } else {
                    $orderItems[$key]['warehouse_id'] = $availableWarehouseStock->warehouse_id;
                }
            } else {
                // Backorder disabled: Find ANY warehouse that has sufficient stock for this item
                $availableWarehouseStock = $variation->productVariationStocks()
                    ->whereIn('warehouse_id', $allWarehouseIds)
                    ->where('stock_qty', '>=', $quantity)
                    ->first();

                if (!$availableWarehouseStock) {
                    return back()->withErrors([
                        'error' => $product->collectTranslation('name') . ' ' . translate('has limited or no stock')
                    ])->withInput();
                }

                // Assign the warehouse that has stock
                $orderItems[$key]['warehouse_id'] = $availableWarehouseStock->warehouse_id;
            }
        }

        // Group items by shop
        $shopItems = [];
        foreach ($orderItems as $item) {
            $shopId = $item['product']->shop_id;
            if (!isset($shopItems[$shopId])) {
                $shopItems[$shopId] = [];
            }
            $shopItems[$shopId][] = $item;
        }

        // Start order submission
        DB::beginTransaction();
        try {
            // Create order group
            $orderGroup = new OrderGroup;
            $orderGroup->user_id = $user->id;
            $orderGroup->name = $name;
            $orderGroup->email = $email ?? '';
            $orderGroup->phone = $normalizedPhone;
            $orderGroup->alternative_phone = $alternativePhone;
            $orderGroup->shipping_address = $shippingAddress . ', ' . ($city?->name ?? '') . ', ' . ($state?->name ?? '');
            $orderGroup->save();

            $grandSubtotal = 0;
            $grandTax = 0;
            $grandShippingCharge = 0;
            $grandDiscount = 0;
            $orderCode = null;

            // Process each shop's items
            foreach ($shopItems as $shopId => $items) {
                $shop = Shop::find($shopId);
                if (!$shop) {
                    continue;
                }

                // Create order for this shop
                $order = new Order;
                $order->user_id = $user->id;
                $order->ip_address = getClientIp();
                $order->order_group_id = $orderGroup->id;
                $order->shop_id = $shopId;
                $order->warehouse_id = $items[0]['warehouse_id'];
                $order->save();

                $shopSubTotal = 0;
                $shopTax = 0;
                $shopShippingCharge = 0;
                $shopDiscount = 0;

                // Calculate shipping for this shop
                $cartItems = array_map(function ($item) use ($shopId) {
                    return [
                        'shop_id' => $shopId,
                        'shipping_class_id' => $item['product']->shipping_class_id ?? null,
                    ];
                }, $items);

                if ($city && $city->zone_id) {
                    $shopShippingCharge = ShippingService::calculate($cityId, $cartItems);
                }

                // Process each item in this shop
                foreach ($items as $item) {
                    $product = $item['product'];
                    $variation = $item['variation'];
                    $quantity = $item['quantity'];

                    // Get stock record for the assigned warehouse (stock already verified earlier)
                    $stock = $variation->productVariationStocks()->where('warehouse_id', $item['warehouse_id'])->first();

                    // Calculate prices
                    $itemTotalPriceWithoutTax = variationPrice($product, $variation, false) * $quantity;
                    $itemTotalTax = (variationPrice($product, $variation) * $quantity) - $itemTotalPriceWithoutTax;
                    $itemTotalDiscount = $itemTotalPriceWithoutTax - (variationDiscountedPrice($product, $variation, false) * $quantity);

                    $shopSubTotal += $itemTotalPriceWithoutTax;
                    $shopTax += $itemTotalTax;
                    $shopDiscount += $itemTotalDiscount;

                    // Create order item
                    $orderItem = new OrderItem;
                    $orderItem->order_id = $order->id;
                    $orderItem->product_variation_id = $variation->id;
                    $orderItem->qty = $quantity;
                    $orderItem->unit_price = variationPrice($product, $variation, false);
                    $orderItem->total_tax = $itemTotalTax;
                    $orderItem->total_discount = $itemTotalDiscount;
                    $orderItem->total_price = $itemTotalPriceWithoutTax + $itemTotalTax - $itemTotalDiscount;
                    $orderItem->unit_cost_price = getAvgPurchasePrice($variation->id);
                    $orderItem->total_cost = $orderItem->unit_cost_price * $orderItem->qty;
                    $orderItem->save();

                    // Update product stock and sales
                    $product->total_sale_count += $quantity;
                    $product->stock_qty -= $quantity;
                    $product->save();

                    // Update variation stock
                    $stock->stock_qty -= $quantity;
                    $stock->save();

                    // Update category sales count
                    $categories = $product->categories;
                    $added = [];
                    foreach ($categories as $cat) {
                        $rootParentCategory = CategoryTrait::getRootParentCategory($cat);
                        if (!in_array($rootParentCategory->id, $added)) {
                            $rootParentCategory->total_sale_count += $quantity;
                            $rootParentCategory->total_sale_amount += $orderItem->total_price;
                            $rootParentCategory->save();
                            $added[] = $rootParentCategory->id;
                        }
                    }

                    // Update brand sales count
                    $brand = $product->brand;
                    if ($brand) {
                        $brand->total_sale_count += $quantity;
                        $brand->total_sale_amount += $orderItem->total_price;
                        $brand->save();
                    }
                }

                // Update order totals
                $order->amount = $shopSubTotal;
                $order->tax_amount = $shopTax;
                $order->shipping_charge_amount = $shopShippingCharge;
                $order->discount_amount = $shopDiscount;
                $order->total_amount = $shopSubTotal + $shopTax + $shopShippingCharge - $shopDiscount;
                $order->save();

                // Create order update
                OrderUpdate::create([
                    'order_id' => $order->id,
                    'user_id' => $user->id,
                    'status' => 'Order Placed',
                    'note' => 'Order has been placed.',
                    'created_by' => $user->id,
                ]);

                // Create notification for shop
                Notification::create([
                    'shop_id' => $shopId,
                    'for' => 'shop',
                    'type' => 'order',
                    'text' => 'Invoice ' . $order->order_code,
                    'link_info' => $order->order_code,
                ]);

                $grandSubtotal += $shopSubTotal;
                $grandTax += $shopTax;
                $grandShippingCharge += $shopShippingCharge;
                $grandDiscount += $shopDiscount;

                if (!$orderCode) {
                    $orderCode = $order->order_code;
                }
            }

            $grandTotal = $grandSubtotal + $grandTax + $grandShippingCharge - $grandDiscount;
            $couponDiscount = 0;

            // Calculate and apply coupon discount at order group level
            if ($coupon && $grandSubtotal > 0) {
                // Validate minimum spend requirement
                if ($grandSubtotal < $coupon->min_spend) {
                    $coupon = null;
                } else {
                    // Check usage limits
                    $totalUsageCount = \App\Models\CouponUsage::where('coupon_code', $coupon->code)->count();
                    $userUsageCount = \App\Models\CouponUsage::where('coupon_code', $coupon->code)
                        ->where('user_id', $user->id)
                        ->count();

                    if ($totalUsageCount >= $coupon->total_usage_limit ||
                        $userUsageCount >= $coupon->customer_usage_limit) {
                        $coupon = null;
                    }
                }
            }

            // Recalculate coupon discount if coupon is still valid after validation
            if ($coupon && $grandSubtotal > 0) {
                if ($coupon->discount_type == 'percentage') {
                    $couponDiscount = $grandSubtotal * ($coupon->discount_value / 100);
                    if ($coupon->max_discount_value && $couponDiscount > $coupon->max_discount_value) {
                        $couponDiscount = $coupon->max_discount_value;
                    }
                } else {
                    $couponDiscount = $coupon->discount_value;
                }

                // Ensure discount doesn't exceed subtotal
                if ($couponDiscount > $grandSubtotal) {
                    $couponDiscount = $grandSubtotal;
                }

                // Apply free shipping if coupon has it
                if ($coupon->is_free_shipping) {
                    $grandShippingCharge = 0;
                }

                // Recalculate grand total with coupon
                $grandTotal = $grandSubtotal + $grandTax + $grandShippingCharge - $grandDiscount - $couponDiscount;

                // Create coupon usage record for audit trail
                // Get the first order from the order group (landing page orders have only 1 order)
                $firstOrder = $orderGroup->orders->first();
                \App\Models\CouponUsage::create([
                    'user_id' => $user->id,
                    'coupon_code' => $coupon->code,
                    'order_id' => $firstOrder ? $firstOrder->id : null,
                ]);

                // Distribute coupon discount proportionally across orders
                $orders = $orderGroup->orders;
                $remainingDiscount = $couponDiscount;
                $orderCount = $orders->count();
                
                foreach ($orders as $index => $order) {
                    if ($grandSubtotal > 0) {
                        // Calculate proportional discount for this order
                        if ($index === $orderCount - 1) {
                            // Last order gets remaining discount to avoid rounding issues
                            $orderCouponDiscount = $remainingDiscount;
                        } else {
                            $orderCouponDiscount = round($couponDiscount * ($order->amount / $grandSubtotal), 2);
                            $remainingDiscount -= $orderCouponDiscount;
                        }

                        // Update order with coupon discount
                        $order->coupon_discount_amount = $orderCouponDiscount;
                        $order->coupon_id = $coupon->id;
                        $order->total_amount = $order->total_amount - $orderCouponDiscount;
                        $order->save();
                    }
                }
            }

            // Create transaction
            $transaction = new Transaction;
            $transaction->amount = $grandSubtotal;
            $transaction->tax_amount = $grandTax;
            $transaction->shipping_charge_amount = $grandShippingCharge;
            $transaction->discount_amount = $grandDiscount + ($couponDiscount ?? 0);
            $transaction->coupon_discount_amount = $couponDiscount ?? 0;
            $transaction->total_amount = $grandTotal;
            $transaction->payment_method = $paymentMethod;
            $transaction->save();

            $orderGroup->transaction_id = $transaction->id;
            $orderGroup->save();

            foreach ($orderGroup->orders as $order) {
                calculateCommission($order);
                syncOrderProfitLog($order);
            }

            // Commit transaction
            DB::commit();

            // Send notifications for COD/Card on delivery
            if ($paymentMethod == 'cash_on_delivery' || $paymentMethod == 'card_on_delivery') {
                try {
                    GlobalNotification::send($user, new OrderPlacedNotification($orderGroup));
                } catch (\Exception $e) {
                    \Log::error('Landing page order notification failed: ' . $e->getMessage());
                }
            }

            // Send SMS notification
            try {
                $smsService = app(SmsService::class);
                if ($user && $user->phone) {
                    $message = $user->name . ', your order has been placed successfully. Order Code: ' . $orderGroup->code;
                    $smsService->send(normalizeBangladeshiNumber($user->phone), $message);
                }
            } catch (\Exception $e) {
                \Log::error('Landing page order SMS failed: ' . $e->getMessage());
            }

            // Check if payment is needed
            $goToPayment = !in_array($paymentMethod, ['cash_on_delivery', 'card_on_delivery']);

            if ($goToPayment) {
                return redirect()->route('payment.page', ['orderCode' => $orderGroup->code]);
            }

            // Redirect to success page
            return redirect()->route('orders.success', ['code' => $orderGroup->code])->with('success', 'Order placed successfully!');

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Landing page order error: ' . $e->getMessage());

            return back()->withErrors(['error' => 'Something went wrong: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Display order success page
     */
    public function orderSuccess($code)
    {
        $orderGroup = OrderGroup::where('code', $code)->first();

        if (!$orderGroup) {
            abort(404, 'Order not found');
        }

        $summary = [
            'customerName' => $orderGroup->user?->name ?? $orderGroup->name,
            'shippingAddress' => $orderGroup->shipping_address ?? '',
            'phone' => $orderGroup->phone ?? '',
            'email' => $orderGroup->email ?? '',
            'paymentMethod' => $orderGroup->transaction?->payment_method ?? '',
            'paymentMethodToShow' => ucfirst(str_replace('_', ' ', $orderGroup->transaction?->payment_method ?? '')),
            'orderCode' => $orderGroup->code,
        ];

        $orders = $orderGroup->orders()->with(['orderItems.productVariation.product', 'shop'])->get();

        return view('frontend.landing.order-success', compact('orderGroup', 'summary', 'orders', 'code'));
    }
}
