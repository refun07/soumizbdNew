<?php

namespace App\Http\Resources\Pos;

use App\Http\Resources\ProductVariationResource;
use Illuminate\Http\Resources\Json\JsonResource;

class PosCartResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $product    = $this->productVariation->product;
        return [
            "id"                    => (int) $this->id,
            'productVariationId'    => (int) $this->product_variation_id,
            'warehouseId'           => (int) $this->warehouse_id,
            "qty"                   => $this->qty,
            'product'               => new PosProductResource($product),
            'variation'             => new PosProductVariationResource($this->productVariation),
        ];
    }
}
