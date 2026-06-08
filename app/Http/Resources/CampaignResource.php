<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CampaignResource extends JsonResource
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
            'name'              => $this->name,
            'slug'              => $this->slug,
            'thumbnailImage'    => uploadedAsset($this->thumbnail_image),
            'banner'            => $this->banner ? uploadedAsset($this->banner) : uploadedAsset($this->thumbnail_image),
            'shortDescription'  => $this->short_description,
            'shopId'            => $this->shop_id,
            'shop'              => $this->shopInfo,
            'startDate'         => $this->start_date,
            'endDate'           => $this->end_date,
        ];

        return $data;
    }
}
