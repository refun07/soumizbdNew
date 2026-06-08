<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Route;

class ShopResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $data =  [
            'id'                    => $this->id,
            'name'                  => $this->name,
            'slug'                  => $this->slug,
            'shopInfo'              => $this->info,
            'rating'                => getShopRatings($this->productReviews()),
            'address'               => $this->address,
            'minOrderAmount'        => (float) $this->min_order_amount,
            'defaultShippingCharge' => (float) $this->default_shipping_charge,
            'logo'                  => uploadedAsset($this->logo),
            'banner'                => uploadedAsset($this->banner),
            'tagline'               => $this->tagline
        ];

        $additionalData = [];

        if (Route::currentRouteName() == 'api.shops.show') {
            // $additionalData['sections'] =   ShopSectionResource::collection($this->shopSections);
        }
        if (Route::currentRouteName() == 'admin.dashboard.topRatedSellers') {
            $additionalData['positiveImpressions'] = $this->impressions()->where('impression', 'positive')->count();
        }
        if (Route::currentRouteName() == 'admin.dashboard.topSellers') {
            $additionalData['ordersAmount'] = $this->orders_sum_total_amount != null ? $this->orders_sum_total_amount : 0;
        }

        if (Route::currentRouteName() == 'admin.dashboard.earningFromSellers') {
            $additionalData['earningFromSellers'] = $this->commission_histories_sum_admin_earning_amount != null ? $this->commission_histories_sum_admin_earning_amount : 0;
        }

        $data = array_merge($data, $additionalData);
        return $data;
    }
}
