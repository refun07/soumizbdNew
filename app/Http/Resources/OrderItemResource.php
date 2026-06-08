<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Route;

class OrderItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $variation    = $this->productVariation;
        $product      = $variation->product;

        $data = [
            'id'                    => $this->id,
            'qty'                   => $this->qty,
            // 'unitPrice'             => $this->unit_price,
            'totalTax'              => $this->total_tax,
            'totalDiscount'         => $this->total_discount,
            'totalPrice'            => $this->total_price,
            'productVariationId'    => $this->product_variation_id,
            'product'               => new ProductResource($product),
            'review'                => new ReviewResource($product->productReviews()->where('user_id', $this->order->user_id)->first()),
            'variation'             => new ProductVariationResource($this->productVariation),
        ];

        return $data;
    }
}
