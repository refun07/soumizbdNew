<?php

namespace App\Http\Controllers\Frontend\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CouponResource;
use App\Models\Cart;
use App\Models\Coupon;
use App\Models\CouponUsage;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    # get all resources with pagination
    public function index(Request $request)
    {
        $limit  = $request->limit ?? perPage();

        $coupons = Coupon::isActive()->where('start_date', '<=', strtotime(date('d-m-Y H:i:s')))->where('end_date', '>=', strtotime(date('d-m-Y H:i:s')))->latest();

        if (config('app.app_mode') == 'singleVendor') {
            $coupons = $coupons->where('shop_id', adminShopId());
        }

        // by search keyword
        if ($request->searchKey != null) {
            $coupons = $coupons->where('code', 'like', '%' . $request->searchKey . '%');
        }

        // by shop
        if ($request->shopSlug != null) {
            $coupons = $coupons->whereHas('shopInfo', function ($q) use ($request) {
                $q->where('slug', $request->shopSlug);
            });
        }

        $coupons   = $coupons->paginate($limit);

        return [
            'success'   => true,
            'status'    => 200,
            'message'   => '',
            'result'    => CouponResource::collection($coupons)->response()->getData(true)
        ];
    }

    # apply coupon here
    public function apply(Request $request)
    {
        $guestUserId  = $request->guestUserId;
        $reqShopId    = $request->shopId;
        $coupon       = Coupon::where('code', $request->code)->first();

        if ($coupon) {
            if ($coupon->shop_id != $reqShopId) {
                return response()->json([
                    'success' => false,
                    'message' => translate('The coupon is invalid for this shop.')
                ], 403);
            }

            $date = strtotime(date('d-m-Y H:i:s'));

            # check if coupon is not expired
            if ($coupon->start_date <= $date && $coupon->end_date >= $date) {

                if (auth('sanctum')->check()) {
                    $carts  = Cart::whereHas('productVariation')->where('user_id', apiUserId())
                        ->whereIn('warehouse_id', session('WarehouseIds'))
                        ->whereHas('productVariation', function ($q) use ($reqShopId) {
                            $q->whereHas('product', function ($q) use ($reqShopId) {
                                $q->where('shop_id', $reqShopId);
                            });
                        })
                        ->get();
                } else {
                    $carts  = Cart::whereHas('productVariation')->where('guest_user_id', (int) $guestUserId)
                        ->whereIn('warehouse_id', session('WarehouseIds'))
                        ->whereHas('productVariation', function ($q) use ($reqShopId) {
                            $q->whereHas('product', function ($q) use ($reqShopId) {
                                $q->where('shop_id', $reqShopId);
                            });
                        })
                        ->get();
                }

                if ($carts && count($carts) == 0) {
                    return $this->__couponApplyFailed(translate('Add product to your cart first'));
                }

                # check min spend
                $subTotal = (float) getSubtotal($carts);

                if ($subTotal >= (float) $coupon->min_spend) {

                    # check if coupon is for categories or products
                    if ($coupon->product_ids || $coupon->category_ids) {
                        if ($carts && validateCouponForProductsAndCategories($carts, $coupon)) {
                            // check coupon usage limit
                            $totalUsageCount = CouponUsage::where('coupon_code', $coupon->code)->count();
                            $userUsageCount  = CouponUsage::where('user_id', apiUserId())->where('coupon_code', $coupon->code)->count();

                            if ($totalUsageCount >= $coupon->total_usage_limit || $userUsageCount >= $coupon->customer_usage_limit) {
                                # coupon used limit reached   
                                return $this->__couponApplyFailed(translate('Coupon usage limit reached'));
                            }

                            return $this->__couponApplySuccessful($coupon);
                        }

                        # coupon not valid for your cart items  
                        return $this->__couponApplyFailed(translate('Coupon is only applicable for selected products or categories'));
                    }

                    // check coupon usage limit
                    $totalUsageCount = CouponUsage::where('coupon_code', $coupon->code)->count();
                    $userUsageCount  = CouponUsage::where('user_id', apiUserId())->where('coupon_code', $coupon->code)->count();

                    if ($totalUsageCount >= $coupon->total_usage_limit || $userUsageCount >= $coupon->customer_usage_limit) {
                        # coupon used limit reached
                        return $this->__couponApplyFailed(translate('Coupon usage limit reached'));
                    }

                    # SUCCESS::can apply coupon - coupon is not product or category based 
                    return $this->__couponApplySuccessful($coupon);
                }

                # min spend
                return $this->__couponApplyFailed('Please shop for at least ' . formatPrice($coupon->min_spend));
            }

            # expired 
            return $this->__couponApplyFailed(translate('Coupon is expired'));
        }

        // coupon not found 
        return $this->__couponApplyFailed(translate('Coupon is not valid for this shop'));
    }

    # coupon apply successfully
    private function __couponApplySuccessful($coupon)
    {
        return [
            'success'   => true,
            'status'    => 200,
            'message'   => translate('Coupon applied successfully'),
            'result'    => [
                'shopId'            => $coupon->shop_id,
                'code'              => $coupon->code,
                'discountType'      => $coupon->discount_type,
                'discount'          => $coupon->discount_value,
                'isFreeShipping'    => $coupon->is_free_shipping,
                'maxDiscountAmount' => $coupon->max_discount_value,
            ]
        ];
    }

    # coupon apply failed
    private function __couponApplyFailed($message = '')
    {
        return response()->json([
            'success'   => false,
            'status'    => 403,
            'message'   => $message,
            'result'    => null
        ], 403);
    }

    # calculate coupon discount here
    public function calculateDiscount($carts, $coupon)
    {
        $date = strtotime(date('d-m-Y H:i:s'));

        # check if coupon is not expired
        if ($coupon->start_date <= $date && $coupon->end_date >= $date) {

            # check min spend
            $subTotal = (float) getSubtotal($carts);

            if ($subTotal >= (float) $coupon->min_spend) {

                # check if coupon is for categories or products
                if ($coupon->product_ids || $coupon->category_ids) {
                    if ($carts && validateCouponForProductsAndCategories($carts, $coupon)) {
                        // check coupon usage limit
                        $totalUsageCount = CouponUsage::where('coupon_code', $coupon->code)->count();
                        $userUsageCount  = CouponUsage::where('user_id', apiUserId())->where('coupon_code', $coupon->code)->count();

                        if ($totalUsageCount >= $coupon->total_usage_limit || $userUsageCount >= $coupon->customer_usage_limit) {
                            # coupon used limit reached  
                            return [
                                'applicable'   => false,
                                'freeShipping' => false,
                                'amount'       => 0,
                            ];
                        }

                        return $this->__discount($coupon, $subTotal);
                    }

                    # coupon not valid for your cart items  
                    return [
                        'applicable'   => false,
                        'freeShipping' => false,
                        'amount'       => 0,
                    ];
                }

                // check coupon usage limit
                $totalUsageCount = CouponUsage::where('coupon_code', $coupon->code)->count();
                $userUsageCount  = CouponUsage::where('user_id', apiUserId())->where('coupon_code', $coupon->code)->count();

                if ($totalUsageCount >= $coupon->total_usage_limit || $userUsageCount >= $coupon->customer_usage_limit) {
                    # coupon used limit reached  
                    return [
                        'applicable'   => false,
                        'freeShipping' => false,
                        'amount'       => 0,
                    ];
                }

                # SUCCESS::can apply coupon - coupon is not product or category based 
                return $this->__discount($coupon, $subTotal);
            }

            # min spend
            return [
                'applicable'   => false,
                'freeShipping' => false,
                'amount'       => 0,
            ];
        }

        # expired 
        return [
            'applicable'   => false,
            'freeShipping' => false,
            'amount'       => 0,
        ];
    }

    # coupon discount
    private function __discount($coupon, $subTotal)
    {
        $amount = 0;
        if ($coupon->discount_type == "amount") {
            $amount = $coupon->discount_value;
        } else {
            $amount = ($subTotal * $coupon->discount_value) / 100;
        }

        if ($amount > $coupon->max_discount_value) {
            $amount = $coupon->max_discount_value;
        }

        return [
            'applicable'   => true,
            'freeShipping' => $coupon->is_free_shipping,
            'amount'       => $amount,
        ];
    }

    # show coupon details
    public function show($code)
    {
        $coupon = Coupon::where('code', $code)->first();
        if (!is_null($coupon)) {
            return [
                'success'   => true,
                'status'    => 200,
                'message'   => '',
                'result'    => new CouponResource($coupon)
            ];
        }

        return response()->json([
            'success'   => true,
            'status'    => 404,
            'message'   => translate('Coupon not found'),
            'result'    => null
        ], 404);
    }
}
