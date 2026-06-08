<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class TopBrandResource extends JsonResource
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
            'thumbnailImage'   => uploadedAsset($this->thumbnail_image),
            'totalSaleCount'    => (int) $this->total_sale_count,
            'totalSaleAmount'   => (float) $this->total_sale_amount,
        ];
    }
}
