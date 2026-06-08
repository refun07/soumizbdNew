<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductBadgeResource extends JsonResource
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
            'id'            => $this->id,
            'name'          => $this->collectTranslation('name'),
            'textColor'     => $this->color,
            'bgColor'       => $this->bg_color,
        ];

        return $data;
    }
}
