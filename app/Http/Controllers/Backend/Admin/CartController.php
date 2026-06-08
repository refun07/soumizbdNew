<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CheckoutSession;
use App\Models\Country;
use App\Models\Coupon;
use App\Models\CouponUsage;
use App\Models\Order;
use App\Models\OrderGroup;
use App\Models\OrderItem;
use App\Models\OrderUpdate;
use App\Models\Shop;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CartController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:view_orders'])->only(['indexPage', 'index', 'show', 'getAgents', 'getFilterData', 'getCountries']);
        $this->middleware(['permission:manage_assigned_orders|manage_orders'])->only(['assignAgent', 'confirmOrder', 'validateCoupon']);
    }

    private function getTransformedCarts($query, $threshold, $authUser = null)
    {
        $allCarts = $query->get();

        $groupedCarts = $allCarts->groupBy(function ($cart) {
            return $cart->user_id ? 'user_'.$cart->user_id : 'guest_'.$cart->guest_user_id;
        });

        $guestIds = $allCarts->whereNull('user_id')->pluck('guest_user_id')->unique()->filter()->values()->toArray();
        $userIds = $allCarts->whereNotNull('user_id')->pluck('user_id')->unique()->values()->toArray();

        $guestCheckoutSessions = CheckoutSession::where('is_converted', false)
            ->whereIn('guest_user_id', $guestIds)
            ->with(['assignedTo' => function ($q) {
                $q->select('id', 'name', 'email');
            }])
            ->get()
            ->groupBy('guest_user_id')
            ->map(function ($sessions) {
                return $sessions->sortByDesc('updated_at')->first();
            });

        $userCheckoutSessions = CheckoutSession::where('is_converted', false)
            ->whereIn('user_id', $userIds)
            ->with(['assignedTo' => function ($q) {
                $q->select('id', 'name', 'email');
            }])
            ->get()
            ->groupBy('user_id')
            ->map(function ($sessions) {
                return $sessions->sortByDesc('updated_at')->first();
            });

        $staffAssignedSessionIds = null;
        if ($authUser && $authUser->user_type === 'staff') {
            $staffAssignedSessionIds = CheckoutSession::where('assigned_to', $authUser->id)
                ->pluck('id')
                ->toArray();
        }

        $transformedCarts = $groupedCarts->map(function ($customerCarts) use ($threshold, $guestCheckoutSessions, $userCheckoutSessions, $staffAssignedSessionIds) {
            $firstCart = $customerCarts->first();

            $totalQty = 0;
            $totalAmount = 0;
            $itemCount = $customerCarts->count();
            $mostRecentUpdate = $customerCarts->max('updated_at');
            $maxMinutesInactive = $customerCarts->min('minutes_inactive');

            $items = $customerCarts->map(function ($cart) use (&$totalQty, &$totalAmount) {
                $price = $cart->variation_price ?? 0;
                $discountValue = $cart->variation_discount_value ?? 0;
                $discountType = $cart->variation_discount_type ?? 'amount';

                if ($discountValue > 0) {
                    if ($discountType === 'percent') {
                        $discountedPrice = $price - ($price * $discountValue / 100);
                    } else {
                        $discountedPrice = $price - $discountValue;
                    }
                    $discountedPrice = max(0, $discountedPrice);
                } else {
                    $discountedPrice = $price;
                }

                $itemTotal = $discountedPrice * $cart->qty;
                $totalQty += $cart->qty;
                $totalAmount += $itemTotal;

                return [
                    'id' => $cart->id,
                    'product' => $cart->product_id ? [
                        'id' => $cart->product_id,
                        'name' => $cart->product_name,
                        'thumbnail' => $cart->product_thumbnail_image ? uploadedAsset($cart->product_thumbnail_image) : null,
                        'slug' => $cart->product_slug,
                    ] : null,
                    'variation' => $cart->product_variation_id ? [
                        'id' => $cart->product_variation_id,
                        'sku' => $cart->variation_sku,
                        'image' => $cart->variation_image,
                    ] : null,
                    'shop' => $cart->shop_id ? [
                        'id' => $cart->shop_id,
                        'name' => $cart->shop_name,
                        'logo' => $cart->shop_logo,
                    ] : null,
                    'quantity' => $cart->qty,
                    'unit_price' => $discountedPrice,
                    'total' => $itemTotal,
                    'created_at' => $cart->created_at,
                    'updated_at' => $cart->updated_at,
                ];
            });

            $isRegistered = $firstCart->user_id !== null;
            $checkoutSession = null;

            if ($isRegistered) {
                $checkoutSession = $userCheckoutSessions->get($firstCart->user_id);
                $isContactable = ! empty($firstCart->user_email) || ! empty($firstCart->user_phone);
            } else {
                $checkoutSession = $guestCheckoutSessions->get($firstCart->guest_user_id);
                $isContactable = $checkoutSession && (
                    ! empty($checkoutSession->customer_email) || ! empty($checkoutSession->customer_phone)
                );
            }

            if (! $isContactable && $checkoutSession) {
                $isContactable = ! empty($checkoutSession->customer_email) || ! empty($checkoutSession->customer_phone);
            }

            $assignedAgent = null;
            if ($checkoutSession && $checkoutSession->assignedTo) {
                $assignedAgent = [
                    'id' => $checkoutSession->assignedTo->id,
                    'name' => $checkoutSession->assignedTo->name,
                    'email' => $checkoutSession->assignedTo->email,
                ];
            }

            if ($staffAssignedSessionIds !== null) {
                if (! $checkoutSession || ! in_array($checkoutSession->id, $staffAssignedSessionIds)) {
                    return null;
                }
            }

            $checkoutSessionData = null;
            if ($checkoutSession) {
                $checkoutSessionData = [
                    'id' => $checkoutSession->id,
                    'customer_name' => $checkoutSession->customer_name,
                    'customer_email' => $checkoutSession->customer_email,
                    'customer_phone' => $checkoutSession->customer_phone,
                    'customer_note' => $checkoutSession->customer_note,
                    'country_id' => $checkoutSession->country_id,
                    'state_id' => $checkoutSession->state_id,
                    'city_id' => $checkoutSession->city_id,
                    'address_line' => $checkoutSession->address_line,
                    'postal_code' => $checkoutSession->postal_code,
                    'address_type' => $checkoutSession->address_type,
                    'direction' => $checkoutSession->direction,
                    'payment_method' => $checkoutSession->payment_method,
                    'shipping_charge' => $checkoutSession->shipping_charge,
                    'formatted_address' => $checkoutSession->formatted_address,
                    'updated_at' => $checkoutSession->updated_at,
                ];
            }

            return [
                'id' => $firstCart->user_id ? 'user_'.$firstCart->user_id : 'guest_'.$firstCart->guest_user_id,
                'customer' => $firstCart->user_id ? [
                    'id' => $firstCart->user_id,
                    'name' => $firstCart->user_name,
                    'email' => $firstCart->user_email,
                    'phone' => $firstCart->user_phone,
                    'type' => 'registered',
                ] : [
                    'id' => $firstCart->guest_user_id,
                    'name' => 'Guest #'.$firstCart->guest_user_id,
                    'type' => 'guest',
                ],
                'total_qty' => $totalQty,
                'total_amount' => $totalAmount,
                'item_count' => $itemCount,
                'last_active' => $mostRecentUpdate,
                'minutes_inactive' => $maxMinutesInactive,
                'is_abandoned' => $maxMinutesInactive >= $threshold,
                'is_contactable' => $isContactable,
                'assigned_agent' => $assignedAgent,
                'checkout_session' => $checkoutSessionData,
                'items' => $items,
            ];
        })->filter()->values();

        return $transformedCarts;
    }

    private function buildBaseQuery()
    {
        return Cart::query()
            ->leftJoin('users', 'carts.user_id', '=', 'users.id')
            ->leftJoin('product_variations', 'carts.product_variation_id', '=', 'product_variations.id')
            ->leftJoin('products', 'product_variations.product_id', '=', 'products.id')
            ->leftJoin('shops', 'products.shop_id', '=', 'shops.id')
            ->select([
                'carts.*',
                'users.name as user_name',
                'users.email as user_email',
                'users.phone as user_phone',
                'product_variations.sku as variation_sku',
                'product_variations.image as variation_image',
                'product_variations.price as variation_price',
                'product_variations.discount_value as variation_discount_value',
                'product_variations.discount_type as variation_discount_type',
                'products.id as product_id',
                'products.name as product_name',
                'products.thumbnail_image as product_thumbnail_image',
                'products.slug as product_slug',
                'shops.id as shop_id',
                'shops.name as shop_name',
                'shops.logo as shop_logo',
                DB::raw('TIMESTAMPDIFF(MINUTE, carts.updated_at, NOW()) as minutes_inactive'),
            ]);
    }

    public function indexPage()
    {
        $threshold = (int) getSetting('abandonedCartThreshold', 60);
        $authUser = user();

        $query = $this->buildBaseQuery();
        $transformedCarts = $this->getTransformedCarts($query, $threshold, $authUser);

        $perPage = 20;
        $currentPage = request()->input('page', 1);
        $total = $transformedCarts->count();
        $lastPage = (int) ceil($total / $perPage);
        $currentPage = min($currentPage, $lastPage) ?: 1;

        $paginatedCarts = $transformedCarts->forPage($currentPage, $perPage)->values();

        $shops = Shop::select('id', 'name')->orderBy('name')->get();
        $agents = User::whereIn('user_type', ['admin', 'staff'])->select('id', 'name', 'email')->orderBy('name')->get();

        $symbolAlignMent = [
            'symbol_first',
            'amount_first',
            'symbol_space',
            'amount_space',
        ];

        $settings = [
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
        ];

        return view('backend.admin.carts.index', [
            'carts' => [
                'data' => $paginatedCarts,
                'meta' => [
                    'current_page' => $currentPage,
                    'last_page' => $lastPage,
                    'per_page' => $perPage,
                    'total' => $total,
                ],
            ],
            'filterData' => [
                'shops' => $shops,
                'agents' => $agents,
            ],
            'settings' => $settings,
        ]);
    }

    public function index(Request $request)
    {
        $threshold = (int) getSetting('abandonedCartThreshold', 60);
        $authUser = user();

        $query = $this->buildBaseQuery();

        if ($request->boolean('abandoned')) {
            $query->where('carts.updated_at', '<', now()->subMinutes($threshold));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('users.name', 'like', "%{$search}%")
                    ->orWhere('users.email', 'like', "%{$search}%")
                    ->orWhere('users.phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('carts.created_at', '>=', $request->input('date_from'));
        }
        if ($request->filled('date_to')) {
            $query->whereDate('carts.created_at', '<=', $request->input('date_to'));
        }

        $transformedCarts = $this->getTransformedCarts($query, $threshold, $authUser);

        if ($request->boolean('contactable')) {
            $transformedCarts = $transformedCarts->filter(function ($cart) {
                return $cart['is_contactable'] === true;
            })->values();
        }

        $perPage = $request->input('per_page', 20);
        $currentPage = $request->input('page', 1);
        $total = $transformedCarts->count();
        $lastPage = (int) ceil($total / $perPage);
        $currentPage = min($currentPage, $lastPage) ?: 1;

        $paginatedCarts = $transformedCarts->forPage($currentPage, $perPage)->values();

        return response()->json([
            'success' => true,
            'data' => $paginatedCarts,
            'meta' => [
                'current_page' => $currentPage,
                'last_page' => $lastPage,
                'per_page' => $perPage,
                'total' => $total,
            ],
        ]);
    }

    public function show($customerId)
    {
        $threshold = (int) getSetting('abandonedCartThreshold', 60);

        $parts = explode('_', $customerId);
        if (count($parts) !== 2) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid customer ID format',
            ], 400);
        }

        $type = $parts[0];
        $id = $parts[1];

        $query = Cart::query()
            ->leftJoin('users', 'carts.user_id', '=', 'users.id')
            ->leftJoin('product_variations', 'carts.product_variation_id', '=', 'product_variations.id')
            ->leftJoin('products', 'product_variations.product_id', '=', 'products.id')
            ->leftJoin('shops', 'products.shop_id', '=', 'shops.id')
            ->select([
                'carts.*',
                'users.name as user_name',
                'users.email as user_email',
                'users.phone as user_phone',
                'product_variations.sku as variation_sku',
                'product_variations.image as variation_image',
                'product_variations.price as variation_price',
                'product_variations.discount_value as variation_discount_value',
                'product_variations.discount_type as variation_discount_type',
                'products.id as product_id',
                'products.name as product_name',
                'products.thumbnail_image as product_thumbnail_image',
                'products.slug as product_slug',
                'products.description as product_description',
                'shops.id as shop_id',
                'shops.name as shop_name',
                'shops.logo as shop_logo',
                'shops.address as shop_address',
                DB::raw('TIMESTAMPDIFF(MINUTE, carts.updated_at, NOW()) as minutes_inactive'),
            ]);

        if ($type === 'user') {
            $query->where('carts.user_id', $id);
        } else {
            $query->where('carts.guest_user_id', $id);
        }

        $customerCarts = $query->get();

        if ($customerCarts->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Cart not found',
            ], 404);
        }

        $firstCart = $customerCarts->first();

        $totalQty = 0;
        $totalAmount = 0;
        $itemCount = $customerCarts->count();
        $mostRecentUpdate = $customerCarts->max('updated_at');
        $maxMinutesInactive = $customerCarts->min('minutes_inactive');

        $items = $customerCarts->map(function ($cart) use (&$totalQty, &$totalAmount) {
            $price = $cart->variation_price ?? 0;
            $discountValue = $cart->variation_discount_value ?? 0;
            $discountType = $cart->variation_discount_type ?? 'amount';

            if ($discountValue > 0) {
                if ($discountType === 'percent') {
                    $discountedPrice = $price - ($price * $discountValue / 100);
                } else {
                    $discountedPrice = $price - $discountValue;
                }
                $discountedPrice = max(0, $discountedPrice);
            } else {
                $discountedPrice = $price;
            }

            $itemTotal = $discountedPrice * $cart->qty;
            $totalQty += $cart->qty;
            $totalAmount += $itemTotal;

            return [
                'id' => $cart->id,
                'product' => $cart->product_id ? [
                    'id' => $cart->product_id,
                    'name' => $cart->product_name,
                    'thumbnail' => $cart->product_thumbnail_image ? uploadedAsset($cart->product_thumbnail_image) : null,
                    'slug' => $cart->product_slug,
                    'description' => $cart->product_description,
                ] : null,
                'variation' => $cart->product_variation_id ? [
                    'id' => $cart->product_variation_id,
                    'sku' => $cart->variation_sku,
                    'image' => $cart->variation_image,
                    'price' => $cart->variation_price,
                    'discount_value' => $cart->variation_discount_value,
                    'discount_type' => $cart->variation_discount_type,
                ] : null,
                'shop' => $cart->shop_id ? [
                    'id' => $cart->shop_id,
                    'name' => $cart->shop_name,
                    'logo' => $cart->shop_logo,
                    'address' => $cart->shop_address,
                ] : null,
                'quantity' => $cart->qty,
                'unit_price' => $discountedPrice,
                'total' => $itemTotal,
                'created_at' => $cart->created_at,
                'updated_at' => $cart->updated_at,
            ];
        });

        $isRegistered = $firstCart->user_id !== null;

        $checkoutSessionQuery = CheckoutSession::where('is_converted', false)
            ->with(['country', 'state', 'city', 'assignedTo' => function ($q) {
                $q->select('id', 'name', 'email');
            }]);

        if ($type === 'user') {
            $checkoutSessionQuery->where('user_id', $id);
        } else {
            $checkoutSessionQuery->where('guest_user_id', $id);
        }

        $checkoutSession = $checkoutSessionQuery->latest('updated_at')->first();

        $isContactable = false;
        if ($isRegistered) {
            $isContactable = ! empty($firstCart->user_email) || ! empty($firstCart->user_phone);
        }
        if ($checkoutSession) {
            if (! $isContactable) {
                $isContactable = ! empty($checkoutSession->customer_email) || ! empty($checkoutSession->customer_phone);
            }
        }

        $checkoutSessionData = null;
        $assignedAgent = null;
        if ($checkoutSession) {
            $checkoutSessionData = [
                'id' => $checkoutSession->id,
                'customer_name' => $checkoutSession->customer_name,
                'customer_email' => $checkoutSession->customer_email,
                'customer_phone' => $checkoutSession->customer_phone,
                'customer_note' => $checkoutSession->customer_note,
                'country_id' => $checkoutSession->country_id,
                'state_id' => $checkoutSession->state_id,
                'city_id' => $checkoutSession->city_id,
                'address_line' => $checkoutSession->address_line,
                'postal_code' => $checkoutSession->postal_code,
                'address_type' => $checkoutSession->address_type,
                'direction' => $checkoutSession->direction,
                'payment_method' => $checkoutSession->payment_method,
                'shipping_charge' => $checkoutSession->shipping_charge,
                'formatted_address' => $checkoutSession->formatted_address,
                'updated_at' => $checkoutSession->updated_at,
            ];

            if ($checkoutSession->assignedTo) {
                $assignedAgent = [
                    'id' => $checkoutSession->assignedTo->id,
                    'name' => $checkoutSession->assignedTo->name,
                    'email' => $checkoutSession->assignedTo->email,
                ];
            }
        }

        $cartData = [
            'id' => $firstCart->user_id ? 'user_'.$firstCart->user_id : 'guest_'.$firstCart->guest_user_id,
            'customer' => $firstCart->user_id ? [
                'id' => $firstCart->user_id,
                'name' => $firstCart->user_name,
                'email' => $firstCart->user_email,
                'phone' => $firstCart->user_phone,
                'type' => 'registered',
            ] : [
                'id' => $firstCart->guest_user_id,
                'name' => 'Guest #'.$firstCart->guest_user_id,
                'type' => 'guest',
            ],
            'total_qty' => $totalQty,
            'total_amount' => $totalAmount,
            'item_count' => $itemCount,
            'last_active' => $mostRecentUpdate,
            'minutes_inactive' => $maxMinutesInactive,
            'is_abandoned' => $maxMinutesInactive >= $threshold,
            'is_contactable' => $isContactable,
            'assigned_agent' => $assignedAgent,
            'items' => $items,
            'checkout_session' => $checkoutSessionData,
        ];

        return response()->json([
            'success' => true,
            'data' => $cartData,
        ]);
    }

    public function assignAgent(Request $request, $customerId)
    {
        $request->validate([
            'agent_id' => 'nullable|integer|exists:users,id',
        ]);

        $parts = explode('_', $customerId);
        if (count($parts) !== 2) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid customer ID format',
            ], 400);
        }

        $type = $parts[0];
        $id = $parts[1];

        $checkoutSessionQuery = CheckoutSession::where('is_converted', false);

        if ($type === 'user') {
            $checkoutSessionQuery->where('user_id', $id);
        } else {
            $checkoutSessionQuery->where('guest_user_id', $id);
        }

        $checkoutSession = $checkoutSessionQuery->latest('updated_at')->first();

        if (! $checkoutSession) {
            $isRegisteredUser = $type === 'user';
            $user = $isRegisteredUser ? User::find($id) : null;

            $isContactable = false;
            if ($user) {
                $isContactable = ! empty($user->email) || ! empty($user->phone);
            } else {
                $guestSession = CheckoutSession::withTrashed()
                    ->where('guest_user_id', $id)
                    ->latest('updated_at')
                    ->first();
                if ($guestSession) {
                    $isContactable = ! empty($guestSession->customer_email) || ! empty($guestSession->customer_phone);
                }
            }

            if (! $isContactable) {
                return response()->json([
                    'success' => false,
                    'message' => 'This customer is not contactable. Only customers with email or phone can be assigned an agent.',
                ], 422);
            }

            $checkoutSession = new CheckoutSession;
            if ($isRegisteredUser) {
                $checkoutSession->user_id = $id;
                $checkoutSession->customer_name = $user->name;
                $checkoutSession->customer_email = $user->email;
                $checkoutSession->customer_phone = $user->phone;
            } else {
                $checkoutSession->guest_user_id = $id;
                $guestSession = CheckoutSession::withTrashed()
                    ->where('guest_user_id', $id)
                    ->latest('updated_at')
                    ->first();
                if ($guestSession) {
                    $checkoutSession->customer_name = $guestSession->customer_name;
                    $checkoutSession->customer_email = $guestSession->customer_email;
                    $checkoutSession->customer_phone = $guestSession->customer_phone;
                }
            }
            $checkoutSession->save();
        }

        if (empty($request->agent_id)) {
            $checkoutSession->assigned_to = null;
            $checkoutSession->save();

            return response()->json([
                'success' => true,
                'message' => 'Agent removed successfully',
                'data' => [
                    'assigned_agent' => null,
                ],
            ]);
        }

        if (empty($checkoutSession->customer_email) && empty($checkoutSession->customer_phone)) {
            $isRegisteredUser = $type === 'user';
            $user = $isRegisteredUser ? User::find($id) : null;
            if (! $user || (empty($user->email) && empty($user->phone))) {
                return response()->json([
                    'success' => false,
                    'message' => 'This customer is not contactable. Only customers with email or phone can be assigned an agent.',
                ], 422);
            }
        }

        $agent = User::find($request->agent_id);
        if (! in_array($agent->user_type, ['admin', 'staff'])) {
            return response()->json([
                'success' => false,
                'message' => 'Selected user is not an admin or staff member',
            ], 422);
        }

        $checkoutSession->assigned_to = $request->agent_id;
        $checkoutSession->save();

        $checkoutSession->load('assignedTo');

        return response()->json([
            'success' => true,
            'message' => 'Agent assigned successfully',
            'data' => [
                'assigned_agent' => $checkoutSession->assignedTo ? [
                    'id' => $checkoutSession->assignedTo->id,
                    'name' => $checkoutSession->assignedTo->name,
                    'email' => $checkoutSession->assignedTo->email,
                ] : null,
            ],
        ]);
    }

    public function getAgents()
    {
        $agents = User::whereIn('user_type', ['admin', 'staff'])
            ->select('id', 'name', 'email')
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $agents,
        ]);
    }

    public function getFilterData()
    {
        $shops = Shop::select('id', 'name')->orderBy('name')->get();
        $agents = User::whereIn('user_type', ['admin', 'staff'])->select('id', 'name', 'email')->orderBy('name')->get();

        return response()->json([
            'success' => true,
            'data' => [
                'shops' => $shops,
                'agents' => $agents,
            ],
        ]);
    }

    private function calculateCouponDiscount($carts, $coupon, $userId = null)
    {
        $date = strtotime(date('d-m-Y H:i:s'));

        if ($coupon->start_date > $date || $coupon->end_date < $date) {
            return ['applicable' => false, 'freeShipping' => false, 'amount' => 0];
        }

        $subTotal = (float) getSubtotal($carts);

        if ($subTotal < (float) $coupon->min_spend) {
            return ['applicable' => false, 'freeShipping' => false, 'amount' => 0];
        }

        $totalUsageCount = CouponUsage::where('coupon_code', $coupon->code)->count();
        $userUsageCount = $userId
            ? CouponUsage::where('user_id', $userId)->where('coupon_code', $coupon->code)->count()
            : 0;

        if ($totalUsageCount >= $coupon->total_usage_limit || $userUsageCount >= $coupon->customer_usage_limit) {
            return ['applicable' => false, 'freeShipping' => false, 'amount' => 0];
        }

        if ($coupon->couponProducts()->count() > 0 || $coupon->couponCategories()->count() > 0) {
            if (! validateCouponForProductsAndCategories($carts, $coupon)) {
                return ['applicable' => false, 'freeShipping' => false, 'amount' => 0];
            }
        }

        $amount = 0;
        if ($coupon->discount_type === 'amount' || $coupon->discount_type === 'flat') {
            $amount = $coupon->discount_value;
        } else {
            $amount = ($subTotal * $coupon->discount_value) / 100;
        }

        if ($amount > $coupon->max_discount_value) {
            $amount = $coupon->max_discount_value;
        }

        return [
            'applicable' => true,
            'freeShipping' => $coupon->is_free_shipping,
            'amount' => $amount,
        ];
    }

    public function getCountries()
    {
        $countries = Country::where('is_active', 1)->select('id', 'name', 'code')->orderBy('name')->get();

        return response()->json([
            'success' => true,
            'data' => $countries,
        ]);
    }

    public function validateCoupon(Request $request)
    {
        $request->validate([
            'coupon_code' => 'required|string',
            'customer_id' => 'required|string',
        ]);

        $coupon = Coupon::where('code', $request->coupon_code)
            ->where('is_active', 1)
            ->first();

        if (! $coupon) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired coupon code',
            ], 404);
        }

        $parts = explode('_', $request->customer_id);
        if (count($parts) !== 2) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid customer ID format',
            ], 400);
        }

        $type = $parts[0];
        $id = $parts[1];

        $cartQuery = Cart::whereNotNull('product_variation_id');
        if ($type === 'user') {
            $cartQuery->where('user_id', $id);
        } else {
            $cartQuery->where('guest_user_id', $id);
        }
        $carts = $cartQuery->with('productVariation.product')->get();

        if ($carts->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No cart items found',
            ], 404);
        }

        $userId = null;
        if ($type === 'user') {
            $userId = (int) $id;
        }

        $result = $this->calculateCouponDiscount($carts, $coupon, $userId);

        if (! $result['applicable']) {
            return response()->json([
                'success' => false,
                'message' => 'Coupon is not applicable to this cart',
                'data' => $result,
            ], 422);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'applicable' => true,
                'amount' => $result['amount'],
                'free_shipping' => $result['freeShipping'],
                'discount_type' => $coupon->discount_type,
                'discount_value' => $coupon->discount_value,
                'coupon_id' => $coupon->id,
                'coupon_code' => $coupon->code,
                'is_free_shipping' => $coupon->is_free_shipping,
            ],
        ]);
    }

    public function confirmOrder(Request $request, $customerId)
    {
        $parts = explode('_', $customerId);
        if (count($parts) !== 2) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid customer ID format',
            ], 400);
        }

        $type = $parts[0];
        $id = $parts[1];

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'note' => 'nullable|string',
            'payment_method' => 'required|string|in:cash_on_delivery,cash,card',
            'coupon_code' => 'nullable|string',
            'shipping_charge' => 'nullable|numeric|min:0',
            'country_id' => 'required|integer|exists:countries,id',
            'state_id' => 'required|integer|exists:states,id',
            'city_id' => 'required|integer|exists:cities,id',
            'address_line' => 'required|string',
        ]);

        $cartQuery = Cart::whereNotNull('product_variation_id');
        if ($type === 'user') {
            $cartQuery->where('user_id', $id);
        } else {
            $cartQuery->where('guest_user_id', $id);
        }
        $carts = $cartQuery->with('productVariation.product')->get();

        if ($carts->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No cart items found for this customer',
            ], 404);
        }

        foreach ($carts as $cart) {
            $productVariation = $cart->productVariation;
            $product = $productVariation?->product;
            if (! $productVariation || ! $product) {
                continue;
            }
            if ($product->stock_qty < $cart->qty) {
                return response()->json([
                    'success' => false,
                    'message' => $product->collectTranslation('name').' has insufficient stock (available: '.$product->stock_qty.', requested: '.$cart->qty.')',
                ], 422);
            }
        }

        DB::beginTransaction();

        try {
            $userId = null;
            if ($type === 'user') {
                $userId = (int) $id;
            } else {
                $checkoutSession = CheckoutSession::where('guest_user_id', $id)
                    ->where('is_converted', false)
                    ->latest('updated_at')
                    ->first();

                if ($checkoutSession && $checkoutSession->customer_phone) {
                    $existingUser = User::where('phone', $checkoutSession->customer_phone)->first();
                    if ($existingUser) {
                        $userId = $existingUser->id;
                    } else {
                        $newUser = User::create([
                            'name' => $request->name,
                            'email' => $request->email,
                            'phone' => $request->phone,
                            'password' => Hash::make('123456'),
                        ]);
                        $userId = $newUser->id;
                    }

                    Cart::where('guest_user_id', $id)->update([
                        'user_id' => $userId,
                        'guest_user_id' => null,
                    ]);
                    $carts = Cart::where('user_id', $userId)
                        ->whereNotNull('product_variation_id')
                        ->with('productVariation.product')
                        ->get();
                } else {
                    $newUser = User::create([
                        'name' => $request->name,
                        'email' => $request->email,
                        'phone' => $request->phone,
                        'password' => Hash::make('123456'),
                    ]);
                    $userId = $newUser->id;
                }
            }

            $address = UserAddress::create([
                'user_id' => $userId,
                'country_id' => $request->country_id,
                'state_id' => $request->state_id,
                'city_id' => $request->city_id,
                'address' => $request->address_line,
                'type' => 'home',
            ]);

            $orderGroup = new OrderGroup;
            $orderGroup->user_id = $userId;
            $orderGroup->name = $request->name;
            $orderGroup->email = $request->email;
            $orderGroup->phone = $request->phone;
            $orderGroup->note = $request->note;
            $orderGroup->shipping_address_id = $address->id;
            $orderGroup->save();

            if ($orderGroup->shippingAddress) {
                $addr = $orderGroup->shippingAddress;
                $orderGroup->shipping_address_type = $addr->type;
                $orderGroup->direction = $addr->direction;
                $orderGroup->shipping_address = implode(', ', array_filter([
                    $addr->address,
                    $addr->area?->name,
                    $addr->city?->name,
                    $addr->state?->name,
                    $addr->postal_code,
                    $addr->country?->name,
                ]));
                $orderGroup->save();
            }

            $grandSubtotal = 0;
            $grandTax = 0;
            $grandShippingCharge = (float) ($request->shipping_charge ?? 0);
            $grandDiscount = 0;
            $grandCouponDiscount = 0;
            $couponId = null;

            if ($request->filled('coupon_code')) {
                $coupon = Coupon::where('code', $request->coupon_code)
                    ->where('is_active', 1)
                    ->first();

                if ($coupon) {
                    $discountResult = $this->calculateCouponDiscount($carts, $coupon, $userId);

                    if ($discountResult['applicable']) {
                        $grandCouponDiscount = $discountResult['amount'];
                        $couponId = $coupon->id;

                        if ($discountResult['freeShipping']) {
                            $grandShippingCharge = 0;
                        }
                    }
                }
            }

            $shopOrders = $carts->groupBy(function ($cart) {
                return $cart->productVariation?->product?->shop_id;
            });

            $shopSubtotals = [];
            foreach ($shopOrders as $shopId => $shopCarts) {
                $sub = 0;
                foreach ($shopCarts as $cart) {
                    $pv = $cart->productVariation;
                    $p = $pv?->product;
                    if ($pv && $p) {
                        $sub += variationPrice($p, $pv, false) * $cart->qty;
                    }
                }
                $shopSubtotals[$shopId] = $sub;
            }
            $totalSubtotalForDistribution = array_sum($shopSubtotals);

            $allOrders = [];

            foreach ($shopOrders as $shopId => $shopCarts) {
                $shopSubTotal = 0;
                $shopTax = 0;
                $shopDiscount = 0;

                $order = new Order;
                $order->user_id = $userId;
                $order->ip_address = $request->ip();
                $order->pos_order = 0;
                $order->order_group_id = $orderGroup->id;
                $order->shop_id = $shopId;
                $order->delivery_status = 'processing';
                $order->save();

                foreach ($shopCarts as $cart) {
                    $productVariation = $cart->productVariation;
                    $product = $productVariation?->product;

                    if (! $productVariation || ! $product) {
                        continue;
                    }

                    $itemTotalPriceWithoutTax = variationPrice($product, $productVariation, false) * $cart->qty;
                    $itemTotalTax = (variationPrice($product, $productVariation) * $cart->qty) - $itemTotalPriceWithoutTax;
                    $itemTotalDiscount = $itemTotalPriceWithoutTax - (variationDiscountedPrice($product, $productVariation, false) * $cart->qty);

                    $shopSubTotal += $itemTotalPriceWithoutTax;
                    $shopTax += $itemTotalTax;
                    $shopDiscount += $itemTotalDiscount;

                    $orderItem = new OrderItem;
                    $orderItem->order_id = $order->id;
                    $orderItem->product_variation_id = $productVariation->id;
                    $orderItem->qty = $cart->qty;
                    $orderItem->unit_price = variationPrice($product, $productVariation, false);
                    $orderItem->total_tax = $itemTotalTax;
                    $orderItem->total_discount = $itemTotalDiscount;
                    $orderItem->total_price = $itemTotalPriceWithoutTax + $itemTotalTax - $itemTotalDiscount;
                    $orderItem->unit_cost_price = getAvgPurchasePrice($productVariation->id);
                    $orderItem->total_cost = $orderItem->unit_cost_price * $orderItem->qty;
                    $orderItem->save();

                    $product->total_sale_count += $cart->qty;
                    $product->stock_qty -= $cart->qty;
                    $product->save();

                    $stock = $productVariation->productVariationStocks()->first();
                    if ($stock) {
                        $stock->stock_qty -= $cart->qty;
                        $stock->save();
                    }

                    $categories = $product->categories;
                    $added = [];
                    foreach ($categories as $cat) {
                        $rootParentCategory = \App\Traits\CategoryTrait::getRootParentCategory($cat);
                        if (! in_array($rootParentCategory->id, $added)) {
                            $rootParentCategory->total_sale_count += $cart->qty;
                            $rootParentCategory->total_sale_amount += $orderItem->total_price;
                            $rootParentCategory->save();
                            array_push($added, $rootParentCategory->id);
                        }
                    }

                    $brand = $product->brand;
                    if ($brand) {
                        $brand->total_sale_count += $cart->qty;
                        $brand->total_sale_amount += $orderItem->total_price;
                        $brand->save();
                    }
                }

                $shopTotalItems = $shopCarts->count();
                $shopCouponShare = $shopTotalItems > 0 && $grandCouponDiscount > 0 && $totalSubtotalForDistribution > 0
                    ? $grandCouponDiscount * ($shopSubtotals[$shopId] / $totalSubtotalForDistribution)
                    : 0;
                $shopShippingShare = $shopOrders->count() > 1
                    ? $grandShippingCharge / $shopOrders->count()
                    : $grandShippingCharge;

                $order->amount = $shopSubTotal;
                $order->tax_amount = $shopTax;
                $order->shipping_charge_amount = $shopShippingShare;
                $order->discount_amount = $shopDiscount;
                $order->coupon_discount_amount = $shopCouponShare;
                $order->coupon_id = $couponId;
                $order->total_amount = $shopSubTotal + $shopTax + $shopShippingShare - $shopDiscount - $shopCouponShare;
                $order->save();

                $grandSubtotal += $shopSubTotal;
                $grandTax += $shopTax;
                $grandDiscount += $shopDiscount;

                OrderUpdate::create([
                    'order_id' => $order->id,
                    'user_id' => $userId,
                    'status' => 'Processing',
                    'note' => 'Order confirmed from abandoned cart.',
                    'created_by' => user()?->id,
                ]);

                $allOrders[] = $order;
            }

            $grandTotal = $grandSubtotal + $grandTax + $grandShippingCharge - $grandDiscount - $grandCouponDiscount;

            if ($couponId && ! empty($allOrders)) {
                foreach ($allOrders as $order) {
                    CouponUsage::create([
                        'user_id' => $userId,
                        'coupon_code' => $request->coupon_code,
                        'order_id' => $order->id,
                    ]);
                }
            }

            $transaction = new Transaction;
            $transaction->amount = $grandSubtotal;
            $transaction->tax_amount = $grandTax;
            $transaction->shipping_charge_amount = $grandShippingCharge;
            $transaction->discount_amount = $grandDiscount;
            $transaction->coupon_discount_amount = $grandCouponDiscount;
            $transaction->total_amount = $grandTotal;
            $transaction->payment_method = $request->payment_method;
            $transaction->save();

            $orderGroup->transaction_id = $transaction->id;
            $orderGroup->save();

            $isCod = in_array($request->payment_method, ['cash_on_delivery']);

            if (! $isCod) {
                foreach ($allOrders as $order) {
                    calculateCommission($order);
                    syncOrderProfitLog($order);
                    $order->payment_status = 'paid';
                    $order->save();

                    OrderUpdate::create([
                        'order_id' => $order->id,
                        'user_id' => $userId,
                        'status' => 'Paid',
                        'note' => 'Payment received on order confirmation.',
                        'created_by' => user()?->id,
                    ]);
                }

                $transaction->status = 'paid';
                $transaction->save();
            } else {
                foreach ($allOrders as $order) {
                    calculateCommission($order);
                    syncOrderProfitLog($order);
                }
            }

            $checkoutSession = CheckoutSession::where('is_converted', false);
            if ($type === 'user') {
                $checkoutSession->where('user_id', $id);
            } else {
                $checkoutSession->where('guest_user_id', $id);
            }
            $activeSession = $checkoutSession->latest('updated_at')->first();
            if ($activeSession) {
                if ($activeSession->assigned_to) {
                    foreach ($allOrders as $order) {
                        $order->agent_id = $activeSession->assigned_to;
                        $order->save();
                        calculateAgentCommissionFromSession($order, $activeSession);
                    }
                }
                $activeSession->delete();
            }

            Cart::destroy($carts->pluck('id')->toArray());

            DB::commit();

            $firstOrder = $allOrders[0] ?? null;
            $orderCode = $firstOrder ? getSetting('order_code_prefix').$firstOrder->order_code : 'N/A';

            return response()->json([
                'success' => true,
                'message' => 'Order confirmed successfully',
                'data' => [
                    'order_code' => $orderCode,
                    'order_group_id' => $orderGroup->id,
                    'payment_method' => $request->payment_method,
                    'total_amount' => $grandTotal,
                    'is_paid' => ! $isCod,
                ],
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            \Log::error('Confirm Order Error: '.$th->getMessage().' - '.$th->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Something went wrong while confirming the order',
            ], 500);
        }
    }
}
