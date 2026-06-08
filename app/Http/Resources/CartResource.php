<?php

namespace App\Http\Resources;

use App\Http\Resources\ProductVariationResource;
use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
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
            'userId'                => (int) $this->user_id,
            'guestUserId'           => (int) $this->guest_user_id,
            'productVariationId'    => (int) $this->product_variation_id,
            'warehouseId'           => (int) $this->warehouse_id,
            "qty"                   => $this->qty,
            'shop'                  => new ShopResource($product->shopInfo),
            'product'               => new ProductResource($product),
            'variation'             => new ProductVariationResource($this->productVariation),
        ];
    }
}
