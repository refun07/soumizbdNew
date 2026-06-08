<?php

namespace App\Http\Resources\Campaign;

use Illuminate\Http\Resources\Json\JsonResource;

class CampaignVariationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $product = $this->product;
        return [
            'id'                => $this->id,
            'productId'         => (int) $this->product_id,
            'product'           => new CampaignProductResource($product),
            'discountType'      => $this->discount_type,
            'discountValue'     => $this->discount_value,
            'variation'         => new CampaignProductVariationResource($this->productVariation),
            'hasVariation'      => (int) $product->has_variation
        ];
    }
}
