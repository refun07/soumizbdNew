<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Route;

class BrandResource extends JsonResource
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
            'name'              => $this->collectTranslation('name'),
            'slug'              => $this->slug,
            'thumbnailImage'    => uploadedAsset($this->collectTranslation('thumbnail_image')),
            'bannerImage'       => uploadedAsset($this->collectTranslation('banner_image') ?: $this->meta_image ?: $this->thumbnail_image),
            'metaImage'         => uploadedAsset($this->collectTranslation('meta_image')),
            'productsCount'     => $this->whenCounted('products'),
            'brandGroup'        => new BrandGroupResource($this->whenLoaded('brandGroup')),
        ];

        return $data;
    }
}
