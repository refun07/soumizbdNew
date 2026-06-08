<?php

namespace App\Http\Resources;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Resources\Json\JsonResource;
use Route;

class CouponResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $data = [
            'id'                => $this->id,
            'code'              => $this->code,
            'info'              => $this->info,
            'shopId'            => $this->shop_id,
            'shopSlug'          => $this->shopInfo->slug,
            'discountType'      => $this->discount_type,
            'discount'          => $this->discount_value,
            'isFreeShipping'    => $this->is_free_shipping,
            'startDate'         => date('d M, Y', $this->start_date),
            'endDate'           => date('d M, Y', $this->end_date),
            'endDate'           => date('d M, Y', $this->end_date),
            'minSpend'          => $this->min_spend,
            'maxDiscountAmount' => $this->max_discount_value,
            'banner'            => uploadedAsset($this->banner),
        ];

        $additionalData = [];
        if (Route::is("coupons.show")) {
            $products = $this->products()->take(5)->get();
            $categories = $this->categories()->take(5)->get();

            $additionalData = [
                'conditions'        => $this->conditions,
                'products'          => ProductResource::collection($products),
                'categories'        => CategoryResource::collection($categories),
            ];
        }
        $data  = array_merge($data, $additionalData);

        return $data;
    }
}
