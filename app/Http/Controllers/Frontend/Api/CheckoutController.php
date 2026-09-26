<?php

namespace App\Http\Controllers\Frontend\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CheckoutSession;
use App\Models\City;
use App\Models\Coupon;
use App\Models\CouponUsage;
use App\Models\Notification;
use App\Models\Order;
use App\Models\OrderGroup;
use App\Models\OrderItem;
use App\Models\OrderUpdate;
use App\Models\Product;
use App\Models\Shop;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserAddress;
use App\Models\WarehouseZone;
use App\Models\Zone;
use App\Models\ZoneShippingCharge;
use App\Notifications\OrderPlacedNotification;
use App\Services\FacebookConversionService;
use App\Services\OrderService;
use App\Services\ProductService;
use App\Services\ShippingService;
use App\Services\SmsService;
use App\Traits\CategoryTrait;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Notification as GlobalNotification;

class CheckoutController extends Controller
{
    protected $facebook;

    public function __construct(FacebookConversionService $facebook)
    {
        $this->facebook = $facebook;
    }

    // get shipping charges for shops of the address
    public function getShippingCharge(Request $request)
    {
        try {
            Log::debug('[SHIPPING-API] getShippingCharge called', [
                'city_id' => $request->cityId,
                'user_id' => $request->userId ?? null,
                'guest_user_id' => $request->guestUserId ?? null,
                'coupons' => $request->coupons ?? [],
                'shop_ids' => $request->shopIds ?? null,
                'cart_ids' => $request->cartIds ?? null,
                'items_count' => count($request->items ?? []),
                'all_input' => $request->all(),
            ]);

            $zoneId = $request->zoneId
                ? Zone::isActive()->findOrFail($request->zoneId)->id
                : City::findOrFail($request->cityId)->zone_id;
            Log::debug('[SHIPPING-API] Zone ID resolved', ['zone_id' => $zoneId]);

            $userId = $request->userId ?? null;
            $guestUserId = $request->guestUserId ?? null;
            $couponCodes = $request->coupons ?? [];
            $shopIds = $request->shopIds ?? null;
            $cartIds = $request->cartIds ?? null;
            $items = $request->items ?? [];

            $cartItems = [];

            if (! empty($items)) {
                Log::debug('[SHIPPING-API] Using provided items array', ['items' => $items]);

                $productIds = array_unique(array_column($items, 'product_id'));
                $products = Product::whereIn('id', $productIds)->pluck('shipping_class_id', 'id');

                foreach ($items as $item) {
                    $cartItems[] = [
                        'shop_id' => $item['shop_id'],
                        'shipping_class_id' => $products[$item['product_id']] ?? null,
                        'quantity' => $item['quantity'] ?? 1,
                    ];
                }
                Log::debug('[SHIPPING-API] Parsed cart items from input', ['cart_items' => $cartItems]);
            } else {
                Log::debug('[SHIPPING-API] Fetching cart from database', [
                    'user_id' => $userId,
                    'guest_user_id' => $guestUserId,
                    'cart_ids' => $cartIds,
                    'shop_ids' => $shopIds,
                ]);

                $carts = Cart::query()
                    ->when($userId, function ($q) use ($userId) {
                        $q->where('user_id', $userId);
                    })
                    ->when($guestUserId, function ($q) use ($guestUserId) {
                        $q->where('guest_user_id', $guestUserId);
                    })
                    ->when($cartIds, function ($q) use ($cartIds) {
                        $q->whereIn('id', $cartIds);
                    })
                    ->when($shopIds, function ($q) use ($shopIds) {
                        $q->whereHas('productVariation.product', function ($q2) use ($shopIds) {
                            $q2->whereIn('shop_id', $shopIds);
                        });
                    })
                    ->with(['productVariation.product'])
                    ->get();

                Log::debug('[SHIPPING-API] Carts retrieved from DB', [
                    'cart_count' => $carts->count(),
                    'cart_ids' => $carts->pluck('id')->toArray(),
                ]);

                $cartItems = $carts->map(function ($cart) {
                    $data = [
                        'shop_id' => $cart->productVariation->product->shop_id,
                        'shipping_class_id' => $cart->productVariation->product->shipping_class_id ?? null,
                        'cart_id' => $cart->id,
                        'qty' => $cart->qty,
                        'product_id' => $cart->productVariation->product->id,
                        'variation_id' => $cart->product_variation_id,
                    ];
                    Log::debug('[SHIPPING-API] Cart item detail', $data);

                    return [
                        'shop_id' => $cart->productVariation->product->shop_id,
                        'shipping_class_id' => $cart->productVariation->product->shipping_class_id ?? null,
                    ];
                })->toArray();

                Log::debug('[SHIPPING-API] Parsed cart items from DB', ['cart_items' => $cartItems]);
            }

            Log::debug('[SHIPPING-API] Calling ShippingService::calculate', [
                'city_id' => $request->cityId,
                'zone_id' => $zoneId,
                'cart_items' => $cartItems,
                'coupon_codes' => $couponCodes,
            ]);

            $totalShippingCharge = ShippingService::calculateForZone($zoneId, $cartItems, $couponCodes);

            Log::debug('[SHIPPING-API] ShippingService::calculate returned', [
                'total_shipping_charge' => $totalShippingCharge,
            ]);

            $breakdown = $this->getShippingBreakdown($zoneId, $cartItems);

            Log::debug('[SHIPPING-API] Response prepared', [
                'total_shipping_charge' => $totalShippingCharge,
                'breakdown' => $breakdown,
            ]);

            return response()->json([
                'success' => true,
                'status' => 200,
                'result' => $totalShippingCharge,
                'breakdown' => $breakdown,
            ]);
        } catch (\Exception $e) {
            Log::error('[SHIPPING-API] Error in getShippingCharge', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'city_id' => $request->cityId ?? null,
                'cart_items' => $cartItems ?? [],
            ]);

            return response()->json([
                'success' => false,
                'status' => 500,
                'message' => 'Failed to calculate shipping charge',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function initiateCheckout(Request $request)
    {
        try {
            if (config('app.FACEBOOK_PIXEL_ACTIVATION') != '1') {
                return [
                    'success' => true,
                    'status' => 200,
                    'message' => translate('Tracking disabled'),
                    'result' => null,
                ];
            }

            $user = apiUser();
            $contentIds = $request->input('content_ids', []);
            $contents = $request->input('contents', []);

            $this->facebook->sendEvent('InitiateCheckout', [
                'email' => $request->input('email') ?: $user?->email,
                'phone' => $request->input('phone') ?: $user?->phone,
            ], [
                'value' => (float) $request->input('value', 0),
                'currency' => $request->input('currency', getSetting('currencyCode') ?? 'USD'),
                'content_ids' => is_array($contentIds) ? $contentIds : [],
                'content_type' => 'product',
                'contents' => is_array($contents) ? $contents : [],
                'num_items' => (int) $request->input('num_items', 0),
            ], $request->eventId);

            return [
                'success' => true,
                'status' => 200,
                'message' => translate('Initiate checkout tracked'),
                'result' => null,
            ];
        } catch (\Exception $e) {
            \Log::error('Facebook InitiateCheckout tracking failed: '.$e->getMessage());

            return [
                'success' => false,
                'status' => 500,
                'message' => translate('Failed to track initiate checkout'),
                'result' => null,
            ];
        }
    }

    public function getDeliveryAreas(Request $request)
    {
        $cartItems = $this->getCartItemsForShipping($request);
        $couponCodes = $request->coupons ?? [];

        $areas = Zone::isActive()
            ->whereIn('name', ['Inside Dhaka', 'Outside Dhaka'])
            ->orderByRaw("CASE WHEN name = 'Inside Dhaka' THEN 0 ELSE 1 END")
            ->get()
            ->map(fn (Zone $zone) => [
                'id' => $zone->id,
                'name' => $zone->name,
                'charge' => ShippingService::calculateForZone($zone->id, $cartItems, $couponCodes),
            ])
            ->values();

        return response()->json([
            'success' => true,
            'status' => 200,
            'result' => $areas,
        ]);
    }

    private function getCartItemsForShipping(Request $request): array
    {
        $items = $request->items ?? [];

        if (! empty($items)) {
            $products = Product::whereIn('id', array_unique(array_column($items, 'product_id')))
                ->pluck('shipping_class_id', 'id');

            return collect($items)->map(fn ($item) => [
                'shop_id' => $item['shop_id'],
                'shipping_class_id' => $products[$item['product_id']] ?? null,
                'quantity' => $item['quantity'] ?? 1,
            ])->all();
        }

        return Cart::query()
            ->when($request->userId, fn ($query, $userId) => $query->where('user_id', $userId))
            ->when($request->guestUserId, fn ($query, $guestUserId) => $query->where('guest_user_id', $guestUserId))
            ->when($request->cartIds, fn ($query, $cartIds) => $query->whereIn('id', $cartIds))
            ->with('productVariation.product')
            ->get()
            ->map(fn ($cart) => [
                'shop_id' => $cart->productVariation->product->shop_id,
                'shipping_class_id' => $cart->productVariation->product->shipping_class_id,
                'quantity' => $cart->qty,
            ])
            ->all();
    }

    private function getShippingBreakdown(int $zoneId, array $cartItems): array
    {
        $breakdown = [];

        foreach ($cartItems as $item) {
            $shopId = $item['shop_id'];
            $charge = ShippingService::calculateForZone($zoneId, [$item]);

            $breakdown[] = [
                'shop_id' => $shopId,
                'shop_name' => Shop::find($shopId)->name ?? 'Unknown',
                'charge' => $charge,
            ];
        }

        return $breakdown;
    }

    // store new order
    public function store(Request $request)
    {
        Log::debug('[CHECKOUT] Store method started', [
            'user_id' => auth()->id(),
            'cart_ids' => $request->cartIds,
            'payment_method' => $request->paymentMethod,
            'shipping_address_id' => $request->shippingAddressId,
            'billing_address_id' => $request->billingAddressId,
            'coupon_codes' => $request->couponCodes,
            'shop_ids' => $request->shopIds,
            'request_all' => $request->all(),
        ]);

        $user = apiUser();
        $newAddress = null;
        if (! $user) {
            if (User::where('phone', $request->phone)->exists()) {
                $user = User::where('phone', $request->phone)->first();
                Log::debug('[CHECKOUT] Existing guest user found', ['user_id' => $user->id, 'phone' => $request->phone]);
            } else {
                $user = User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'phone' => $request->phone,
                    'password' => Hash::make($request->password ?? '123456'),
                ]);
                Log::debug('[CHECKOUT] New guest user created', ['user_id' => $user->id, 'phone' => $request->phone]);
            }
        } else {
            Log::debug('[CHECKOUT] Authenticated user', ['user_id' => $user->id]);
        }

        Log::debug('[CHECKOUT] User resolved', ['user_id' => $user->id ?? null]);

        // Transfer guest carts to the resolved user
        if ($request->guestUserId && ! empty($request->cartIds)) {
            $transferredCount = Cart::whereIn('id', $request->cartIds)
                ->where('guest_user_id', $request->guestUserId)
                ->update([
                    'user_id' => $user->id,
                    'guest_user_id' => null,
                ]);

            Log::debug('[CHECKOUT] Transferred guest carts to user', [
                'user_id' => $user->id,
                'guest_user_id' => $request->guestUserId,
                'transferred_count' => $transferredCount,
                'cart_ids' => $request->cartIds,
            ]);
        }

        if (empty($request->cartIds) || empty($request->shopIds)) {
            return response()->json([
                'success' => false,
                'status' => 422,
                'message' => translate('Please select at least one product'),
                'result' => null,
            ], 422);
        }

        if (! $request->shippingAddressId && $request->shippingAddress) {
            $requestAddress = json_decode($request->shippingAddress, true);

            // Find existing address or create new
            $newAddress = UserAddress::where('user_id', $user->id)->first();

            if (! $newAddress) {
                $newAddress = new UserAddress;
                $newAddress->user_id = $user->id;
            }

            // Update address fields from request.
            // Frontend now sends stateId directly and cityId can be optional.
            $countryId = $requestAddress['countryId'] ?? $requestAddress['country']['id'] ?? null;
            $stateId = $requestAddress['stateId'] ?? $requestAddress['state']['id'] ?? $requestAddress['city']['state_id'] ?? null;
            $cityId = $requestAddress['cityId'] ?? $requestAddress['city']['id'] ?? null;
            $addressLine = $requestAddress['address'] ?? null;

            if (! $countryId || ! $addressLine) {
                return response()->json([
                    'success' => false,
                    'status' => 422,
                    'message' => translate('Please select a delivery area and provide address'),
                    'result' => null,
                ], 422);
            }

            $newAddress->country_id = (int) $countryId;
            $newAddress->state_id = $stateId ? (int) $stateId : null;
            $newAddress->city_id = $cityId ? (int) $cityId : null;
            $newAddress->address = $addressLine;
            $newAddress->area_id = $requestAddress['areaId'] ?? $newAddress->area_id;
            $newAddress->postal_code = $requestAddress['postalCode'] ?? $newAddress->postal_code;
            $newAddress->direction = $requestAddress['direction'] ?? $newAddress->direction;
            $newAddress->type = $requestAddress['type'] ?? $newAddress->type;

            $newAddress->save();

            if ($newAddress->wasRecentlyCreated) {
                Log::debug('[CHECKOUT] New shipping address created', ['address_id' => $newAddress->id]);
            } else {
                Log::debug('[CHECKOUT] Existing shipping address updated', ['address_id' => $newAddress->id]);
            }
        } else {
            // shipping address not selected
            if (! $request->shippingAddressId) {
                Log::warning('[CHECKOUT] No shipping address selected');

                return response()->json([
                    'success' => false,
                    'status' => 403,
                    'message' => translate('Please select shipping address'),
                    'result' => null,
                ], 403);
            }
        }

        if (! $request->shippingAddressId && $request->shippingAddress) {
        } else {
            // billing address not selected
            if (! $request->billingAddressId) {
                Log::warning('[CHECKOUT] No billing address selected');

                return response()->json([
                    'success' => false,
                    'status' => 403,
                    'message' => translate('Please select billing address'),
                    'result' => null,
                ], 403);
            }
        }

        // shipment is not available
        $shippingAddress = UserAddress::find($request->shippingAddressId ?? $newAddress->id);
        if (! $shippingAddress) {
            Log::warning('[CHECKOUT] Shipping address not found', ['shipping_address_id' => $request->shippingAddressId ?? $newAddress->id]);

            return response()->json([
                'success' => false,
                'status' => 403,
                'message' => translate('We could not find your address in our system'),
                'result' => null,
            ], 403);
        }

        Log::debug('[CHECKOUT] Shipping address found', [
            'address_id' => $shippingAddress->id,
            'city_id' => $shippingAddress->city_id,
            'zone_id' => $shippingAddress->city?->zone_id,
        ]);

        $shippingZone = $request->shippingZoneId
            ? Zone::isActive()->whereIn('name', ['Inside Dhaka', 'Outside Dhaka'])->find($request->shippingZoneId)
            : $shippingAddress->city?->zone;

        if (! $shippingZone) {
            return response()->json([
                'success' => false,
                'status' => 422,
                'message' => translate('Please select a delivery area'),
                'result' => null,
            ], 422);
        }

        // if (!$shippingAddress->area->zone) {
        //     return response()->json([
        //         'success'   => false,
        //         'status'    => 403,
        //         'message'   => translate('Sorry, delivery is not available in this shipping address'),
        //         'result'    => null
        //     ], 403);
        // }

        // carts - get all carts regardless of warehouse, since location feature is removed

        $carts = Cart::whereHas('productVariation')
            ->whereIn('id', $request->cartIds)
            ->where(function ($query) use ($user, $request) {
                $query->where('user_id', $user->id);

                if ($request->guestUserId) {
                    $query->orWhere('guest_user_id', $request->guestUserId);
                }
            })
            ->get();

        Log::debug('[CHECKOUT] Carts retrieved', ['cart_count' => count($carts), 'cart_ids' => $carts->pluck('id')->toArray()]);

        // empty cart
        if (count($carts) == 0) {
            Log::warning('[CHECKOUT] Cart is empty');

            return response()->json([
                'success' => false,
                'status' => 403,
                'message' => translate('Your cart is empty. Please select a product'),
                'result' => null,
            ], 403);
        }

        // Get all warehouse IDs from database (ignore session, find all warehouses with stock)
        $allWarehouseIds = \App\Models\Warehouse::pluck('id')->toArray();
        $allowBackorder = getSetting('allowBackorder') == '1';
        Log::debug('[CHECKOUT] All warehouse IDs', ['warehouse_ids' => $allWarehouseIds, 'allow_backorder' => $allowBackorder]);

        foreach ($carts as $cart) {
            if ($allowBackorder) {
                // Backorder enabled: assign to any available warehouse without stock check
                $availableWarehouseStock = $cart->productVariation->productVariationStocks()
                    ->whereIn('warehouse_id', $allWarehouseIds)
                    ->first();

                if (! $availableWarehouseStock) {
                    // If no warehouse stock record exists, create notification but still allow order
                    $productVariation = $cart->productVariation;
                    $product = $productVariation->product;

                    Notification::create([
                        'shop_id' => $product->shop_id,
                        'for' => 'shop',
                        'type' => 'stock',
                        'text' => 'SKU - '.$productVariation->sku,
                        'link_info' => $product->id,
                    ]);

                    Log::warning('[CHECKOUT] No warehouse stock record (backorder enabled)', [
                        'cart_id' => $cart->id,
                        'variation_id' => $productVariation->id,
                        'sku' => $productVariation->sku,
                        'requested_qty' => $cart->qty,
                    ]);
                }

                // Assign to first available warehouse or create new warehouse assignment if none exists
                $warehouseId = $availableWarehouseStock ? $availableWarehouseStock->warehouse_id : ($allWarehouseIds[0] ?? null);
                if ($warehouseId && $cart->warehouse_id != $warehouseId) {
                    $cart->warehouse_id = $warehouseId;
                    $cart->save();
                    Log::debug('[CHECKOUT] Cart assigned to warehouse (backorder)', [
                        'cart_id' => $cart->id,
                        'warehouse_id' => $warehouseId,
                    ]);
                }
            } else {
                // Backorder disabled: Find ANY warehouse that has sufficient stock for this cart item
                // Skip stock check for pre-order items (they will be validated separately)
                $isPreOrderEnabled = $cart->productVariation->isPreOrderEnabled();

                $availableWarehouseStock = $cart->productVariation->productVariationStocks()
                    ->whereIn('warehouse_id', $allWarehouseIds)
                    ->when(! $isPreOrderEnabled, function ($query) use ($cart) {
                        $query->where('stock_qty', '>=', $cart->qty);
                    })
                    ->first();

                if (! $availableWarehouseStock && ! $isPreOrderEnabled) {
                    $productVariation = $cart->productVariation;
                    $product = $productVariation->product;

                    Notification::create([
                        'shop_id' => $product->shop_id,
                        'for' => 'shop',
                        'type' => 'stock',
                        'text' => 'SKU - '.$productVariation->sku,
                        'link_info' => $product->id,
                    ]);

                    Log::warning('[CHECKOUT] Insufficient stock', [
                        'cart_id' => $cart->id,
                        'variation_id' => $productVariation->id,
                        'sku' => $productVariation->sku,
                        'requested_qty' => $cart->qty,
                    ]);

                    return response()->json([
                        'success' => false,
                        'status' => 403,
                        'message' => $product->collectTranslation('name').' '.translate('has limited or no stock'),
                        'result' => null,
                    ], 403);
                }

                Log::debug('[CHECKOUT] Warehouse stock found', [
                    'cart_id' => $cart->id,
                    'warehouse_id' => $availableWarehouseStock->warehouse_id,
                    'available_stock' => $availableWarehouseStock->stock_qty,
                ]);

                // Reassign cart to warehouse with sufficient stock
                if ($cart->warehouse_id != $availableWarehouseStock->warehouse_id) {
                    $cart->warehouse_id = $availableWarehouseStock->warehouse_id;
                    $cart->save();
                    Log::debug('[CHECKOUT] Cart reassigned to warehouse', [
                        'cart_id' => $cart->id,
                        'old_warehouse_id' => $cart->getOriginal('warehouse_id'),
                        'new_warehouse_id' => $availableWarehouseStock->warehouse_id,
                    ]);
                }
            }
        }

        Log::debug('[CHECKOUT] Warehouse stock check passed', ['user_id' => $user->id, 'cart_ids' => $request->cartIds]);

        // pre-order validation
        foreach ($carts as $cart) {
            $productVariation = $cart->productVariation;
            Log::debug('[CHECKOUT] Checking pre-order for cart item', [
                'cart_id' => $cart->id,
                'variation_id' => $productVariation->id,
                'variation_sku' => $productVariation->sku,
                'variation_pre_order' => $productVariation->pre_order,
                'product_pre_order' => $productVariation->product->pre_order,
            ]);
            $isPreOrderEnabled = $productVariation->isPreOrderEnabled();
            Log::debug('[CHECKOUT] isPreOrderEnabled result', [
                'variation_id' => $productVariation->id,
                'isPreOrderEnabled' => $isPreOrderEnabled,
            ]);
            if ($isPreOrderEnabled) {
                $validation = ProductService::validatePreOrder($productVariation, $user->id ?? null, $cart->qty);
                if (! $validation['valid']) {
                    return response()->json([
                        'success' => false,
                        'status' => 403,
                        'message' => $productVariation->product->collectTranslation('name').': '.$validation['message'],
                        'result' => null,
                    ], 403);
                }
            }
        }

        // get coupon data based on request
        $coupons = collect();
        if ($request->couponCodes && ! empty($request->couponCodes)) {
            $coupons = Coupon::where(function ($query) use ($request) {
                foreach ($request->couponCodes as $couponCode) {
                    $query->orWhere('code', $couponCode);
                }
            })->get();
            Log::debug('[CHECKOUT] Coupons found', ['coupon_codes' => $request->couponCodes, 'coupons_count' => $coupons->count()]);
        } else {
            Log::debug('[CHECKOUT] No coupon codes provided');
        }

        // start order submission
        DB::beginTransaction();
        Log::debug('[CHECKOUT] DB transaction started');

        try {
            $orderGroup = new OrderGroup;
            $orderGroup->user_id = $user->id;
            $orderGroup->name = $request->name;
            $orderGroup->email = $request->email;
            $orderGroup->phone = $request->phone;
            $orderGroup->alternative_phone = $request->alternativePhone;
            $orderGroup->note = $request->note;
            $orderGroup->shipping_address_id = $request->shippingAddressId ?? $newAddress->id;
            $orderGroup->billing_address_id = $request->billingAddressId ?? $newAddress->id;
            $orderGroup->save();

            Log::debug('[CHECKOUT] OrderGroup created', [
                'order_group_id' => $orderGroup->id,
                'order_group_code' => $orderGroup->code,
                'user_id' => $orderGroup->user_id,
            ]);

            if ($orderGroup->shippingAddress) {
                $address = $orderGroup->shippingAddress;
                $orderGroup->shipping_address_type = $address->type;
                $orderGroup->direction = $address->direction;
                $orderGroup->shipping_address = $address->address
                    .', '.$shippingZone->name
                    .', '.($address->country?->name ?? '');
            }
            if ($orderGroup->billingAddress) {
                $address = $orderGroup->billingAddress;
                $orderGroup->billing_address_type = $address->type;
                $orderGroup->billing_address = $address->address
                    .', '.$shippingZone->name
                    .', '.($address->country?->name ?? '');
            }
            $orderGroup->save();

            Log::debug('[CHECKOUT] OrderGroup addresses saved', [
                'order_group_id' => $orderGroup->id,
                'shipping_address' => $orderGroup->shipping_address,
                'billing_address' => $orderGroup->billing_address,
            ]);

            $grandTotal = 0;

            $grandSubtotal = 0;
            $grandTax = 0;
            $grandShippingCharge = 0;
            $grandDiscount = 0;
            $grandCouponDiscount = 0;
            if ($request->shopIds) {
                Log::debug('[CHECKOUT] Processing shops', ['shop_ids' => $request->shopIds]);
                $processedShopCount = 0;
                foreach ($request->shopIds as $shopId) {
                    try {
                        $shop = Shop::find((int) $shopId);
                        Log::debug('[CHECKOUT] Processing shop', ['shop_id' => $shopId, 'shop_name' => $shop?->name]);

                        $shopCarts = Cart::query()
                            ->whereIn('id', $request->cartIds)
                            ->where(function ($query) use ($user, $request) {
                                $query->where('user_id', $user->id);

                                if ($request->guestUserId) {
                                    $query->orWhere('guest_user_id', $request->guestUserId);
                                }
                            })
                            ->whereHas('productVariation', function ($q) use ($shopId) {
                                $q->whereHas('product', function ($q) use ($shopId) {
                                    $q->where('shop_id', $shopId);
                                });
                            })
                            ->get();
                        Log::debug('[CHECKOUT] Shop carts retrieved', [
                            'shop_id' => $shopId,
                            'shop_carts_count' => count($shopCarts),
                            'shop_carts_ids' => $shopCarts->pluck('id')->toArray(),
                        ]);
                        if (count($shopCarts) > 0) {
                            Log::debug('[CHECKOUT] Creating order for shop', ['shop_id' => $shopId, 'cart_count' => count($shopCarts)]);
                            // store order here
                            $order = new Order;
                            $order->user_id = $user->id;
                            $order->ip_address = getClientIp();
                            $order->order_group_id = $orderGroup->id;
                            $order->shop_id = $shopId;
                            $order->warehouse_id = $shopCarts->first()->warehouse_id;
                            $cleanShippingAddress = preg_replace('/\s*,\s*,+/', ', ', (string) $orderGroup->shipping_address);
                            $cleanShippingAddress = trim((string) $cleanShippingAddress, ' ,');
                            // $order->shipping_address = $cleanShippingAddress;
                            $order->save();

                            Log::debug('[CHECKOUT] Order created', [
                                'order_id' => $order->id,
                                'order_code' => $order->order_code,
                                'shop_id' => $shopId,
                                'warehouse_id' => $order->warehouse_id,
                            ]);

                            $shopSubTotal = 0;
                            $shopTax = 0;
                            $shopShippingCharge = 0;
                            $shopDiscount = 0;
                            $shopCouponDiscount = 0;

                            $cartItems = $shopCarts->map(function ($cart) use ($shopId) {
                                return [
                                    'shop_id' => $shopId,
                                    'shipping_class_id' => $cart->productVariation->product->shipping_class_id ?? null,
                                    'quantity' => $cart->qty,
                                ];
                            })->toArray();

                            $shopShippingCharge = ShippingService::calculateForZone(
                                $shippingZone->id,
                                $cartItems
                            );

                            // shipping charge
                            // $shippingCharge = ZoneShippingCharge::where('shop_id', $shopId);
                            // if ($shippingAddress->area) {
                            //     $shippingAddress = $shippingAddress->where('zone_id', $shippingAddress->area->zone_id);
                            // }
                            // $shippingCharge = $shippingCharge->first();
                            // if ($shippingCharge) {
                            //     $shopShippingCharge = $shippingCharge->charge;
                            // } else {
                            //     $shopShippingCharge = $shop->default_shipping_charge;
                            // }

                            // shop coupon check
                            $coupon = null;
                            if ($request->couponCodes && ! empty($request->couponCodes)) {
                                $coupon = $coupons->firstWhere('shop_id', $shopId);

                                //  discount calculation & coupon usage & free shipping
                                if ($coupon) {
                                    $shopCouponDetails = (new CouponController)->calculateDiscount($shopCarts, $coupon);

                                    if ($shopCouponDetails['applicable'] == true) {

                                        // free shipping
                                        if ($shopCouponDetails['freeShipping'] == true) {
                                            $shopShippingCharge = 0;
                                        }

                                        $shopCouponDiscount = $shopCouponDetails['amount'];

                                        $coupon_usage = new CouponUsage;
                                        $coupon_usage->user_id = $user->id;
                                        $coupon_usage->order_id = $order->id;
                                        $coupon_usage->coupon_code = $coupon->code;
                                        $coupon_usage->save();

                                        $order->coupon_id = $coupon->id;
                                    }
                                }
                            }

                            foreach ($shopCarts as $shopCart) {
                                $productVariation = $shopCart->productVariation;
                                $product = $productVariation->product;

                                Log::debug('[CHECKOUT] Processing cart item', [
                                    'cart_id' => $shopCart->id,
                                    'variation_id' => $productVariation->id,
                                    'variation_sku' => $productVariation->sku,
                                    'variation_pre_order' => $productVariation->pre_order,
                                    'product_pre_order' => $productVariation->product->pre_order,
                                ]);

                                $itemTotalPriceWithoutTax = variationPrice($product, $productVariation, false) * $shopCart->qty;
                                $itemTotalTax = (variationPrice($product, $productVariation) * $shopCart->qty) - $itemTotalPriceWithoutTax;
                                $itemTotalDiscount = $itemTotalPriceWithoutTax - (variationDiscountedPrice($product, $productVariation, false) * $shopCart->qty);

                                $shopSubTotal += $itemTotalPriceWithoutTax;
                                $shopTax += $itemTotalTax;
                                $shopDiscount += $itemTotalDiscount;

                                $isPreOrder = $productVariation->isPreOrderEnabled();
                                Log::debug('[CHECKOUT] isPreOrderEnabled for order item', [
                                    'variation_id' => $productVariation->id,
                                    'isPreOrder' => $isPreOrder,
                                ]);

                                if ($isPreOrder) {
                                    $reservation = ProductService::reservePreOrder($productVariation, $shopCart->qty);
                                    if (! $reservation['success']) {
                                        throw new \Exception($reservation['message']);
                                    }
                                }

                                $orderItem = new OrderItem;
                                $orderItem->order_id = $order->id;
                                $orderItem->product_variation_id = $shopCart->product_variation_id;
                                $orderItem->qty = $shopCart->qty;
                                $orderItem->unit_price = variationPrice($product, $productVariation, false);
                                $orderItem->total_tax = $itemTotalTax;
                                $orderItem->total_discount = $itemTotalDiscount;
                                $orderItem->total_price = $itemTotalPriceWithoutTax + $itemTotalTax - $itemTotalDiscount;
                                $orderItem->unit_cost_price = getAvgPurchasePrice($shopCart->product_variation_id);
                                $orderItem->total_cost = $orderItem->unit_cost_price * $orderItem->qty;
                                if ($isPreOrder) {
                                    Log::debug('[CHECKOUT] Setting pre_order_info', [
                                        'variation_id' => $productVariation->id,
                                        'expected_date' => $productVariation->getExpectedReleaseDate(),
                                    ]);
                                    $orderItem->pre_order_info = [
                                        'is_pre_order' => true,
                                        'expected_date' => $productVariation->getExpectedReleaseDate(),
                                        'deposit_paid' => 0,
                                    ];
                                } else {
                                    Log::debug('[CHECKOUT] NOT setting pre_order_info (isPreOrder=false)', [
                                        'variation_id' => $productVariation->id,
                                    ]);
                                }
                                $orderItem->save();
                                Log::debug('[CHECKOUT] Order item saved', [
                                    'order_item_id' => $orderItem->id,
                                    'product_variation_id' => $orderItem->product_variation_id,
                                    'pre_order_info' => $orderItem->pre_order_info,
                                ]);

                                // manage stock and sales
                                $product->total_sale_count += $orderItem->qty;
                                $product->stock_qty -= $orderItem->qty;
                                $product->save();

                                // product variation stock
                                $stock = $productVariation->productVariationStocks()->where('warehouse_id', $shopCart->warehouse_id)->first();
                                if ($stock) {
                                    $stock->stock_qty -= $orderItem->qty;
                                    $stock->save();
                                } elseif (! $isPreOrder && ! $allowBackorder) {
                                    throw new \RuntimeException('Stock record is unavailable for this product.');
                                }

                                // category sales count for only root category
                                $categories = $product->categories;
                                $added = [];
                                foreach ($categories as $cat) {
                                    $rootParentCategory = CategoryTrait::getRootParentCategory($cat);
                                    if (! in_array($rootParentCategory->id, $added)) {
                                        $rootParentCategory->total_sale_count += $orderItem->qty;
                                        $rootParentCategory->total_sale_amount += $orderItem->total_price;
                                        $rootParentCategory->save();
                                        array_push($added, $rootParentCategory->id);
                                    }
                                }

                                // sales brands count
                                $brand = $product->brand;
                                if ($brand) {
                                    $brand->total_sale_count += $orderItem->qty;
                                    $brand->total_sale_amount += $orderItem->total_price;
                                    $brand->save();
                                }
                            }

                            $order->amount = $shopSubTotal;
                            $order->tax_amount = $shopTax;
                            $order->shipping_charge_amount = $shopShippingCharge;
                            $order->discount_amount = $shopDiscount;
                            $order->coupon_discount_amount = $shopCouponDiscount;
                            $order->total_amount = $shopSubTotal + $shopTax + $shopShippingCharge - $shopDiscount - $shopCouponDiscount;
                            $order->save();
                            $processedShopCount++;

                            // Send Facebook Purchase event
                            try {
                                if ($user && config('app.FACEBOOK_PIXEL_ACTIVATION') == '1') {

                                    // Build content_ids & contents arrays
                                    $orderItems = OrderItem::where('order_id', $order->id)->get();

                                    $content_ids = [];
                                    $contents = [];

                                    foreach ($orderItems as $orderItem) {
                                        $variation = $orderItem->productVariation;
                                        $product = $variation->product;

                                        $variantId = "{$product->id}-{$variation->id}";

                                        $content_ids[] = $variantId;

                                        $contents[] = [
                                            'id' => $variantId,
                                            'quantity' => $orderItem->qty,
                                            'item_price' => $orderItem->unit_price,
                                        ];
                                    }

                                    $this->facebook->sendEvent('Purchase', [
                                        'email' => $user->email,
                                        'phone' => $user->phone,
                                    ], [
                                        'value' => $order->total_amount,
                                        'currency' => getSetting('currencyCode') ?? 'USD',
                                        'content_ids' => $content_ids,  // <-- FIXED (variant IDs)
                                        'content_type' => 'product',
                                        'contents' => $contents,     // <-- FIXED (details)
                                        'num_items' => array_sum(array_column($contents, 'quantity')),
                                    ], $request->eventId);
                                }
                            } catch (\Exception $e) {
                                \Log::error('Facebook Pixel tracking failed: '.$e->getMessage());
                            }

                            // ---------------------> commissions calculated in payment success <---------------------

                            // order update
                            OrderUpdate::create([
                                'order_id' => $order->id,
                                'user_id' => $user->id,
                                'status' => 'Order Placed',
                                'note' => 'Order has been placed.',
                                'created_by' => $user->id,
                            ]);

                            // notification
                            Notification::create([
                                'shop_id' => $shopId,
                                'for' => 'shop',
                                'type' => 'order',
                                'text' => 'Invoice '.$order->order_code,
                                'link_info' => $order->order_code,
                            ]);

                            // grand summary
                            $grandSubtotal += $shopSubTotal;
                            $grandTax += $shopTax;
                            $grandShippingCharge += $shopShippingCharge;
                            $grandDiscount += $shopDiscount;
                            $grandCouponDiscount += $shopCouponDiscount;
                        }
                    } catch (\Exception $e) {
                        \Log::error('Shop checkout processing failed for shop ID '.$shopId.': '.$e->getMessage());
                        throw $e;
                    }
                }

                if ($processedShopCount === 0) {
                    throw new \RuntimeException('No valid shop products were found for this order.');
                }

                $grandTotal = $grandSubtotal + $grandTax + $grandShippingCharge - $grandDiscount - $grandCouponDiscount;
                Log::debug('[CHECKOUT] Grand totals calculated', [
                    'grand_subtotal' => $grandSubtotal,
                    'grand_tax' => $grandTax,
                    'grand_shipping_charge' => $grandShippingCharge,
                    'grand_discount' => $grandDiscount,
                    'grand_coupon_discount' => $grandCouponDiscount,
                    'grand_total' => $grandTotal,
                ]);
                // transaction
                $transaction = new Transaction;
                $transaction->amount = $grandSubtotal;
                $transaction->tax_amount = $grandTax;
                $transaction->shipping_charge_amount = $grandShippingCharge;
                $transaction->discount_amount = $grandDiscount;
                $transaction->coupon_discount_amount = $grandCouponDiscount;
                $transaction->total_amount = $grandTotal;
                $transaction->payment_method = $request->paymentMethod;
                $transaction->save();

                Log::debug('[CHECKOUT] Transaction created', [
                    'transaction_id' => $transaction->id,
                    'total_amount' => $transaction->total_amount,
                    'payment_method' => $transaction->payment_method,
                ]);

                $orderGroup->transaction_id = $transaction->id;
                $orderGroup->save();

                Log::debug('[CHECKOUT] OrderGroup transaction linked', [
                    'order_group_id' => $orderGroup->id,
                    'transaction_id' => $transaction->id,
                ]);

                // clear user's cart
                Cart::destroy($request->cartIds);
                Log::debug('[CHECKOUT] Cart cleared', ['cart_ids' => $request->cartIds]);
            }
            // complete transaction
            DB::commit();
            Log::debug('[CHECKOUT] DB transaction committed');

            // send notifications for offline methods (order is placed without an online gateway)
            if (in_array($request->paymentMethod, ['cash_on_delivery', 'card_on_delivery', 'bkash_personal'])) {
                try {
                    // OrderPlaced::dispatch($orderGroup);
                    GlobalNotification::send($user, new OrderPlacedNotification($orderGroup));
                    Log::debug('[CHECKOUT] Order placed notification sent', ['user_id' => $user->id, 'order_group_id' => $orderGroup->id]);
                } catch (\Exception $e) {
                    Log::error('[CHECKOUT] Failed to send order placed notification', ['error' => $e->getMessage()]);
                }
            }

            try {
                $sms = app(SmsService::class);
                $user = $orderGroup->user;
                if ($user && $user->phone && $user->phone != '') {
                    $to = $user->phone;
                    $message = $user->name.' Your Payment has been placed successfully.'.' In '.env('APP_NAME').'Order Code:'.$orderGroup->code;
                    $sms->send(normalizeBangladeshiNumber($to), $message);
                }
            } catch (\Exception $e) {
                Log::error('[CHECKOUT] Failed to send SMS', ['error' => $e->getMessage()]);
            }

            Log::debug('[CHECKOUT] Order process completed successfully', [
                'order_group_id' => $orderGroup->id,
                'order_group_code' => $orderGroup->code,
                'transaction_id' => $transaction->id ?? null,
                'payment_method' => $request->paymentMethod,
            ]);

            // Delete checkout session after order is placed
            try {
                $checkoutSession = CheckoutSession::where('is_converted', false)
                    ->where(function ($q) use ($user, $request) {
                        if ($request->guestUserId) {
                            $q->where('guest_user_id', $request->guestUserId);
                        } elseif ($user && $user->id) {
                            $q->where('user_id', $user->id);
                        }
                    })
                    ->latest()
                    ->first();

                if ($checkoutSession) {
                    if ($checkoutSession->assigned_to) {
                        foreach ($orderGroup->orders as $order) {
                            $order->agent_id = $checkoutSession->assigned_to;
                            $order->save();
                            calculateAgentCommissionFromSession($order, $checkoutSession);
                        }
                    }
                    $checkoutSession->delete();
                    Log::debug('[CHECKOUT-SESSION] Session deleted after order', [
                        'session_id' => $checkoutSession->id,
                        'order_code' => $orderGroup->code,
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('[CHECKOUT-SESSION] Failed to delete session after order', [
                    'error' => $e->getMessage(),
                    'order_code' => $orderGroup->code,
                ]);
            }

            return response()->json([
                'success' => true,
                'status' => 200,
                'message' => translate('Your order has been placed successfully'),
                'result' => [
                    'orderCode' => $orderGroup->code,
                    'goToPayment' => ! in_array($request->paymentMethod, ['cash_on_delivery', 'card_on_delivery', 'bkash_personal']),
                    'paymentMethod' => $request->paymentMethod,
                ],
            ], 200);
        } catch (\Exception $e) {
            Log::error('[CHECKOUT] Checkout store error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => $user->id ?? null,
                'cart_ids' => $request->cartIds ?? null,
            ]);
            DB::rollback();
            Log::debug('[CHECKOUT] DB transaction rolled back');

            return response()->json([
                'success' => false,
                'status' => 500,
                'message' => translate('Something went wrong'),
                'result' => null,
            ], 500);
        }
    }

    // payment success
    public static function paymentSuccess($payment_details = null, $orderGroupId = null)
    {
        if ($orderGroupId == null) {
            $orderGroupId = session('order_group_id');
        }

        $orderGroup = OrderGroup::where('id', $orderGroupId)->first();
        $orders = $orderGroup->orders;

        foreach ($orders as $order) {
            calculateCommission($order);
            syncOrderProfitLog($order);
            $order->payment_status = 'paid';
            $order->save();

            if ($orderGroup->user_id) {
                OrderUpdate::create([
                    'order_id' => $order->id,
                    'user_id' => $orderGroup->user_id,
                    'status' => 'Paid',
                    'note' => 'Payment status updated to Paid.',
                    'created_by' => $orderGroup->user_id,
                ]);
            }
        }

        $transaction = $orderGroup->transaction;
        $transaction->status = 'paid';
        $transaction->payment_details = $payment_details ? json_encode($payment_details) : null;
        $transaction->save();

        try {
            GlobalNotification::send($orderGroup->user, new OrderPlacedNotification($orderGroup));
        } catch (\Exception $e) {
        }
        try {
            $sms = app(SmsService::class);
            $user = $orderGroup->user;
            if ($user && $user->phone && $user->phone != '') {
                $to = $user->phone;
                $message = $user->name.' Your Payment has been placed successfully.'.' In '.env('APP_NAME').'Order Code:'.$orderGroup->code;
                $sms->send(normalizeBangladeshiNumber($to), $message);
            }
        } catch (\Exception $e) {
        }

        clearPaymentSession();
        $redirect_to = route('home').'/orders/success/'.$orderGroup->code;

        return redirect($redirect_to);
    }

    // payment failed
    public static function paymentFailed($orderGroupId = null)
    {
        if ($orderGroupId == null) {
            $orderGroupId = session('order_group_id');
        }

        $orderGroup = OrderGroup::where('id', $orderGroupId)->first();

        if (! is_null($orderGroup)) {
            $transaction = $orderGroup->transaction;
            $orders = $orderGroup->orders;

            $orderService = new OrderService;

            foreach ($orders as $order) {
                $order->delivery_status = 'cancelled';
                $order->save();
                $orderService->addQtyToStock($order);

                if ($orderGroup->user_id) {
                    OrderUpdate::create([
                        'order_id' => $order->id,
                        'user_id' => $orderGroup->user_id,
                        'status' => 'Cancelled',
                        'note' => 'Delivery status updated to Cancelled.',
                        'created_by' => $orderGroup->user_id,
                    ]);
                }
            }

            $transaction->status = 'failed';
            $transaction->save();
        }

        clearPaymentSession();
        $redirect_to = route('home').'/orders/failed';

        return redirect($redirect_to);
    }

    // update zone
    public function updateZone(Request $request)
    {
        // carts
        $carts = Cart::whereHas('productVariation')->where('user_id', auth('sanctum')->user()->id)->get();
        $allowBackorder = getSetting('allowBackorder') == '1';

        // stock check
        foreach ($carts as $cart) {
            $productVariation = $cart->productVariation;
            $product = $productVariation->product;
            $shopId = $product->shop_id;

            $warehouseZone = WarehouseZone::where('shop_id', $shopId)->where('zone_id', $request->newZoneId)->first();
            if (is_null($warehouseZone)) {
                $cart->delete();

                continue;
            }

            $stock = $productVariation->productVariationStocks()->where('warehouse_id', $warehouseZone->warehouse_id)->first();

            if ($allowBackorder) {
                // Backorder enabled: skip stock checks, just assign warehouse
                $cart->warehouse_id = $warehouseZone->warehouse_id;
                $cart->save();

                continue;
            }

            if (is_null($stock)) {
                $cart->delete();

                continue;
            }

            if ($stock->stock_qty == 0) {
                $cart->delete();

                continue;
            }

            if ($stock->stock_qty < $cart->qty) {
                $cart->qty = $stock->stock_qty;
                $cart->save();

                // notification
                Notification::create([
                    'shop_id' => $shopId,
                    'for' => 'shop',
                    'type' => 'stock',
                    'text' => 'SKU - '.$productVariation->sku,
                    'link_info' => $product->id,
                ]);

                continue;
            }

            $cart->warehouse_id = $warehouseZone->warehouse_id;
            $cart->save();
        }
        $cartController = app(CartController::class);

        return $cartController->index($request, translate('Cart updated successfully'));
    }

    // save checkout session data (real-time as user types)
    public function saveSession(Request $request)
    {
        try {
            // Validate input data
            $validated = $request->validate([
                'customerDetails.name' => 'nullable|string|max:255',
                'customerDetails.email' => 'nullable|email|max:255',
                'customerDetails.phone' => 'nullable|string|max:50',
                'customerDetails.note' => 'nullable|string',
                'shippingAddress.countryId' => 'nullable|integer',
                'shippingAddress.country.id' => 'nullable|integer',
                'shippingAddress.stateId' => 'nullable|integer',
                'shippingAddress.state.id' => 'nullable|integer',
                'shippingAddress.cityId' => 'nullable|integer',
                'shippingAddress.city.id' => 'nullable|integer',
                'shippingAddress.address' => 'nullable|string|max:1000',
                'shippingAddress.postalCode' => 'nullable|string|max:20',
                'shippingAddress.type' => 'nullable|string|in:home,office,other',
                'shippingAddress.direction' => 'nullable|string|max:500',
                'paymentMethod' => 'nullable|string|max:50',
                'currentStep' => 'nullable|string|in:details,shipping,payment',
                'cartData' => 'nullable|array',
                'cartTotal' => 'nullable|numeric',
                'shippingCharge' => 'nullable|numeric',
                'guestUserId' => 'nullable|integer',
            ]);

            $userId = auth('sanctum')->id();
            $guestUserId = $request->guestUserId;

            if (! $userId && ! $guestUserId) {
                return response()->json([
                    'success' => false,
                    'status' => 400,
                    'message' => translate('User or guest ID required'),
                ], 400);
            }

            // Find existing session or create new one
            $session = CheckoutSession::findOrCreateForUser($userId, $guestUserId);

            if (! $session) {
                return response()->json([
                    'success' => false,
                    'status' => 500,
                    'message' => translate('Failed to create checkout session'),
                ], 500);
            }

            // Update customer details if provided
            if ($request->has('customerDetails')) {
                $customerDetails = $request->customerDetails;
                $session->customer_name = $customerDetails['name'] ?? $session->customer_name;
                $session->customer_email = $customerDetails['email'] ?? $session->customer_email;
                $session->customer_phone = $customerDetails['phone'] ?? $session->customer_phone;
                $session->customer_note = $customerDetails['note'] ?? $session->customer_note;
            }

            // Update shipping address if provided
            if ($request->has('shippingAddress')) {
                $shippingAddress = $request->shippingAddress;
                $session->country_id = $shippingAddress['countryId']
                    ?? (is_array($shippingAddress['country'] ?? null) ? ($shippingAddress['country']['id'] ?? null) : null)
                    ?? $session->country_id;
                $session->state_id = $shippingAddress['stateId']
                    ?? (is_array($shippingAddress['state'] ?? null) ? ($shippingAddress['state']['id'] ?? null) : null)
                    ?? $session->state_id;
                $session->city_id = $shippingAddress['cityId']
                    ?? (is_array($shippingAddress['city'] ?? null) ? ($shippingAddress['city']['id'] ?? null) : null)
                    ?? $session->city_id;
                $session->address_line = $shippingAddress['address'] ?? $session->address_line;
                $session->postal_code = $shippingAddress['postalCode'] ?? $session->postal_code;
                $session->address_type = $shippingAddress['type'] ?? $session->address_type;
                $session->direction = $shippingAddress['direction'] ?? $session->direction;
            }

            // Update payment method if provided
            if ($request->has('paymentMethod')) {
                $session->payment_method = $request->paymentMethod;
            }

            // Update current step if provided
            if ($request->has('currentStep')) {
                $session->current_step = $request->currentStep;
            }

            // Update cart data if provided
            if ($request->has('cartData')) {
                $session->cart_data = $request->cartData;
            }

            // Update cart total if provided
            if ($request->has('cartTotal')) {
                $session->cart_total = $request->cartTotal;
            }

            // Update shipping charge if provided
            if ($request->has('shippingCharge')) {
                $session->shipping_charge = $request->shippingCharge;
            }

            $session->save();

            Log::debug('[CHECKOUT-SESSION] Session saved', [
                'session_id' => $session->id,
                'user_id' => $userId,
                'guest_user_id' => $guestUserId,
                'current_step' => $session->current_step,
            ]);

            return response()->json([
                'success' => true,
                'status' => 200,
                'message' => translate('Checkout session saved'),
                'result' => [
                    'session_id' => $session->id,
                ],
            ], 200);

        } catch (\Exception $e) {
            Log::error('[CHECKOUT-SESSION] Error saving session', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'status' => 500,
                'message' => translate('Failed to save checkout session'),
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // get checkout session data (for pre-filling form when user returns)
    public function getSession(Request $request)
    {
        try {
            $userId = auth('sanctum')->id();
            $guestUserId = $request->guestUserId;

            if (! $userId && ! $guestUserId) {
                return response()->json([
                    'success' => false,
                    'status' => 400,
                    'message' => translate('User or guest ID required'),
                ], 400);
            }

            $query = CheckoutSession::where('is_converted', false);

            if ($userId) {
                $query->where('user_id', $userId);
            } else {
                $query->where('guest_user_id', $guestUserId);
            }

            $session = $query->latest()->first();

            if (! $session) {
                return response()->json([
                    'success' => true,
                    'status' => 200,
                    'message' => translate('No active checkout session found'),
                    'result' => null,
                ], 200);
            }

            // Load relationships for full address data
            $session->load(['country', 'state', 'city']);

            $result = [
                'customerDetails' => [
                    'name' => $session->customer_name,
                    'email' => $session->customer_email,
                    'phone' => $session->customer_phone,
                    'note' => $session->customer_note,
                ],
                'shippingAddress' => $session->address_line ? [
                    // Note: No 'id' field - this is temporary session data, not a saved UserAddress
                    'countryId' => $session->country_id,
                    'country' => $session->country,
                    'stateId' => $session->state_id,
                    'state' => $session->state,
                    'cityId' => $session->city_id,
                    'city' => $session->city,
                    'address' => $session->address_line,
                    'postalCode' => $session->postal_code,
                    'type' => $session->address_type,
                    'direction' => $session->direction,
                ] : null,
                'paymentMethod' => $session->payment_method,
                'currentStep' => $session->current_step,
                'cartData' => $session->cart_data,
                'cartTotal' => $session->cart_total,
                'shippingCharge' => $session->shipping_charge,
                'isConverted' => $session->is_converted,
                'lastUpdated' => $session->updated_at,
            ];

            return response()->json([
                'success' => true,
                'status' => 200,
                'message' => translate('Checkout session retrieved'),
                'result' => $result,
            ], 200);

        } catch (\Exception $e) {
            Log::error('[CHECKOUT-SESSION] Error getting session', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'status' => 500,
                'message' => translate('Failed to retrieve checkout session'),
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
