<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CollectionResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'            => $this->id,
            'name'          => $this->name,
            'slug'          => $this->slug,
            'image'         => $this->image ? uploadedAsset($this->image) : null,
            'sortOrder'     => (int) $this->sort_order,
            'productsCount' => $this->whenCounted('products'),
        ];
    }
}
