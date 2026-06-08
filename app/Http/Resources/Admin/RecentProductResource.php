<?php

namespace App\Http\Resources\Admin;

use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Resources\Json\JsonResource;

class RecentProductResource extends JsonResource
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
            'name'              => $this->collectTranslation('name'),
            'slug'              => $this->slug,
            'thumbnailImg'      => uploadedAsset($this->thumbnail_image),
            'unit'              => $this->unit ? $this->unit->collectTranslation('name') : '',
            'basePrice'         => (float) productBasePrice($this, false),
            'totalSaleCount'    => (int) $this->total_sale_count,
        ];
    }
}
