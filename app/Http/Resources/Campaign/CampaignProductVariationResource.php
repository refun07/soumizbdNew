<?php

namespace App\Http\Resources\Campaign;

use Illuminate\Http\Resources\Json\JsonResource;

class CampaignProductVariationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $blockedVariationIds = $request->attributes->get('blockedVariationIds', []);

        return [
            'id'                            => $this->id,
            'productId'                     => (int) $this->product_id,
            'name'                          => generateVariationName($this->code),
            'code'                          => $this->code,
            'sku'                           => $this->sku,
            'image'                         => uploadedAsset($this->image),
            'basePrice'                     => (float) variationPrice($this->product, $this, false),
            'discountedBasePrice'           => (float) variationDiscountedPrice($this->product, $this, false),
            'basePriceWithTax'              => (float) variationPrice($this->product, $this),
            'discountedBasePriceWithTax'    => (float) variationDiscountedPrice($this->product, $this),
            'tax'                           => (float) variationDiscountedPrice($this->product, $this) - (float) variationDiscountedPrice($this->product, $this, false),
            'isCampaignProduct'             => in_array((int) $this->id, $blockedVariationIds, true),
        ];
    }
}
