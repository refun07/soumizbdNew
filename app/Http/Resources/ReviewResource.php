<?php

namespace App\Http\Resources;

use App\Http\Resources\ProductVariationResource;
use App\Models\MediaFile;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;

class ReviewResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {

        $images = [];
        if (!is_null($this->images)) {
            $reviewImages = explode(',', $this->images);
            foreach ($reviewImages as $image) {
                $tempImage        = new MediaFile;
                $tempImage->id    = $image;
                $tempImage->image = uploadedAsset($image);
                $images[$image]   = $tempImage;
            }
        }


        $product = $this->product;

        return [
            "id"            => (int) $this->id,
            'userId'        => (int) $this->user_id,
            'user'          => new UserResource($this->user),
            'rating'        => (float) $this->rating,
            'description'   => $this->description,
            'images'        => $images,
            'imageIds'      => $this->images,
            'product'       => new ProductResource($product),
            'shop'          => new ShopResource($this->shop),
            'createdDate'   => date('d M, Y', strtotime($this->created_at)),
        ];
    }
}
