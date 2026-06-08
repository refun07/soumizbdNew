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
            'thumbnailImage'    => uploadedAsset($this->thumbnail_image)
        ];

        return $data;
    }
}
