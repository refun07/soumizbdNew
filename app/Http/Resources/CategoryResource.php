<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
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
            'id'                    => $this->id,
            'name'                  => $this->collectTranslation('name'),
            'slug'                  => $this->slug,
            'thumbnailImage'        => uploadedAsset($this->thumbnail_image),
            'productsCount'         => $this->productCategories()->count(),
            'parent_category'       => CategoryResource::make($this->whenLoaded('parentCategory')),
            'children_categories'   => CategoryResource::collection($this->whenLoaded('childrenCategories'))
        ];

        return $data;
    }
}
