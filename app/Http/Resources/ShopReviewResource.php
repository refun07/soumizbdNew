<?php

namespace App\Http\Resources;

use App\Http\Resources\ProductVariationResource;
use App\Models\MediaFile;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;

class ShopReviewResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            "id"            => (int) $this->id,
            'shopId'        => (int) $this->shop_id,
            'shop'          => new ShopResource($this->shop),
            'userId'        => (int) $this->user_id,
            'user'          => new UserResource($this->user),
            'impression'    => $this->impression,
            'createdDate'   => date('d M, Y', strtotime($this->created_at)),
        ];
    }
}
