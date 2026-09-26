<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BrandGroupResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'            => $this->id,
            'name'          => $this->name,
            'slug'          => $this->slug,
            'image'         => $this->image ? uploadedAsset($this->image) : null,
            'sortOrder'     => (int) $this->sort_order,
            'brandsCount'   => $this->whenCounted('brands'),
        ];
    }
}
