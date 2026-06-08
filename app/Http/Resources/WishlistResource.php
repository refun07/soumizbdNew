<?php

namespace App\Http\Resources;

use App\Http\Resources\ProductVariationResource;
use App\Http\Services\ProductService;
use Illuminate\Http\Resources\Json\JsonResource;

class WishlistResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $columns    = (new ProductService)->getSpecificColumns();
        $product    = $this->product()->first($columns);
        return [
            "id"                    => (int) $this->id,
            'userId'                => (int) $this->user_id,
            'product'               => new ProductResource($product)
        ];
    }
}
