<?php

namespace App\Services;

use App\Models\Coupon;
use App\Models\Category;
use App\Models\CouponCategory;
use App\Models\CouponCondition;
use App\Models\CouponProduct;
use App\Models\Language;
use App\Models\Product;
use Str;

class CouponService
{
    # get data
    public static function index($request)
    {
        $data = [
            'status'        => 200,
            'message'       => '',
            'result'        => [],
        ];

        $searchKey = null;
        $coupons = Coupon::shop()->latest();
        if ($request->search != null) {
            $coupons = $coupons->where('code', 'like', '%' . $request->search . '%');
            $searchKey = $request->search;
        }

        $coupons = $coupons->paginate(perPage());

        $data = [
            'status'    => 200,
            'message'   => '',
            'result'    => [
                'coupons'   => $coupons,
                'searchKey' => $searchKey,
            ],
        ];

        return $data;
    }

    # return view of create form
    public static function create()
    {
        $products = Product::where('is_published', 1)->shop()->get(['id', 'name']);
        $categories = Category::where('parent_id', 0)
            ->orderBy('sorting_order_level', 'desc')
            ->with('childrenCategories')
            ->get();

        $data = [
            'status'        => 200,
            'message'       => '',
            'result'    => [
                'categories'    => $categories,
                'products'      => $products,
            ],
        ];

        return $data;
    }

    # add new data
    public static function store($request)
    {
        $data = [
            'status'        => 200,
            'message'       => '',
            'result'        => [],
        ];

        if (Coupon::where('code', $request->code)->where('shop_id', shop()->id)->count() > 0) {

            $data = [
                'status'    => 403,
                'message'   => translate('Coupon already exist for this coupon code'),
                'result'    => [],
            ];
            return $data;
        }

        try {
            $coupon                 = new Coupon;
            $coupon->code           = $request->code;

            $coupon->shop_id        = shopId();
            $coupon->discount_type  = $request->discount_type;
            $coupon->discount_value = $request->discount_value;
            $coupon->banner         = $request->banner;
            $coupon->is_free_shipping = $request->is_free_shipping;

            if (Str::contains($request->date_range, '-')) {
                $date_var = explode(" - ", $request->date_range);
            } else {
                $date_var = [date("d-m-Y"), date("d-m-Y")];
            }
            $coupon->start_date = strtotime($date_var[0]);
            $coupon->end_date = strtotime($date_var[1]);

            $coupon->min_spend              = $request->min_spend;
            $coupon->max_discount_value     = $request->max_discount_value;
            $coupon->total_usage_limit      = $request->total_usage_limit;
            $coupon->customer_usage_limit   = $request->customer_usage_limit;

            $coupon->save();

            if ($request->category_ids) {
                $coupon->categories()->sync($request->category_ids);
            }

            if ($request->product_ids) {
                $coupon->products()->sync($request->product_ids);
            }

            foreach ($request->infos as $key => $info) {
                if ($info != "") {
                    $condition = new CouponCondition;
                    $condition->coupon_id = $coupon->id;
                    $condition->text = $info;
                    $condition->save();
                }
            }

            $data = [
                'status'    => 200,
                'message'   => translate('Coupon has been added successfully'),
                'result'    => [],
            ];
            return $data;
        } catch (\Throwable $th) {
            $data = [
                'status'    => 403,
                'message'   => translate('Something went wrong'),
                'result'    => [],
            ];
            return $data;
        }
    }

    # return view of edit form
    public static function edit($request, $id)
    {
        try {
            $lang_key = $request->lang_key;
            $language = Language::where('is_active', 1)->where('code', $lang_key)->first();

            if (!$language) {
                if (!$language) {
                    $data = [
                        'status'    => 403,
                        'message'   => translate('Language you are trying to translate is not available or not active'),
                        'result'    => [],
                    ];
                    return $data;
                }
            }

            $products = Product::where('is_published', 1)->shop()->get(['id', 'name']);
            $coupon = Coupon::findOrFail($id);
            $categories = Category::where('parent_id', 0)
                ->orderBy('sorting_order_level', 'desc')
                ->with('childrenCategories')
                ->get();

            $data = [
                'status'    => 200,
                'message'   => '',
                'result'    => [
                    'coupon'        => $coupon,
                    'categories'    => $categories,
                    'products'      => $products,
                    'lang_key'      => $lang_key,
                ],
            ];
            return $data;
        } catch (\Throwable $th) {
            $data = [
                'status'    => 403,
                'message'   => translate('Something went wrong'),
                'result'    => [],
            ];
            return $data;
        }
    }

    # add new data
    public static function update($request, $id)
    {
        $data = [
            'status'        => 200,
            'message'       => '',
            'result'        => [],
        ];

        try {
            $coupon = Coupon::find($id);
            if (Coupon::where('code', $request->code)->where('id', '!=', $id)->where('shop_id', shop()->id)->count() > 0) {
                $data = [
                    'status'    => 403,
                    'message'   => translate('Coupon already exist for this coupon code'),
                    'result'    => [],
                ];
                return $data;
            }

            $coupon->code               = $request->code;

            $coupon->shop_id            = shopId();
            $coupon->discount_type      = $request->discount_type;
            $coupon->discount_value     = $request->discount_value;
            $coupon->banner             = $request->banner;
            $coupon->is_free_shipping   = $request->is_free_shipping;

            if (Str::contains($request->date_range, '-')) {
                $date_var = explode(" - ", $request->date_range);
            } else {
                $date_var = [date("d-m-Y"), date("d-m-Y")];
            }
            $coupon->start_date = strtotime($date_var[0]);
            $coupon->end_date = strtotime($date_var[1]);

            $coupon->min_spend              = $request->min_spend;
            $coupon->max_discount_value     = $request->max_discount_value;
            $coupon->total_usage_limit      = $request->total_usage_limit;
            $coupon->customer_usage_limit   = $request->customer_usage_limit;

            $coupon->save();

            if ($request->category_ids) {
                $coupon->categories()->sync($request->category_ids);
            }

            if ($request->product_ids) {
                $coupon->products()->sync($request->product_ids);
            }

            $coupon->conditions()->delete();
            foreach ($request->infos as $key => $info) {
                if ($info != "") {
                    $condition = new CouponCondition;
                    $condition->coupon_id = $coupon->id;
                    $condition->text = $info;
                    $condition->save();
                }
            }

            $data = [
                'status'    => 200,
                'message'   => translate('Coupon has been updated successfully'),
                'result'    => [],
            ];
            return $data;
        } catch (\Throwable $th) {
            $data = [
                'status'    => 403,
                'message'   => translate('Something went wrong'),
                'result'    => [],
            ];
            return $data;
        }
    }

    # delete data
    public static function destroy($id)
    {
        try {
            $coupon = Coupon::where('id', $id)->first();
            try {
                CouponCategory::where('coupon_id', $coupon->id)->delete();
                CouponProduct::where('coupon_id', $coupon->id)->delete();
            } catch (\Throwable $th) {
            }

            $coupon->delete();

            $data = [
                'success'   => true,
                'status'    => 200,
                'message'   => translate('Coupon deleted successfully'),
                'result'    => null
            ];

            return $data;
        } catch (\Throwable $th) {
            $data = [
                'success'   => false,
                'status'    => 403,
                'message'   => translate('Something went wrong'),
                'result'    => [],
            ];
            return $data;
        }
    }
}
