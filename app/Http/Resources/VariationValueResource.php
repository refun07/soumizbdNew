<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class VariationValueResource extends JsonResource
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
            'colorCode'         => $this->color_code,
            'name'              => $this->collectTranslation('name')
        ];

        return $data;
    }
}
