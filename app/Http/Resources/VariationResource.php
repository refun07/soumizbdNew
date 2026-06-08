<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class VariationResource extends JsonResource
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
            'isColor'           => (bool) $this->is_color,
            'variationValues'   => VariationValueResource::collection($this->variationValues()->isActive()->get())
        ];

        return $data;
    }
}
