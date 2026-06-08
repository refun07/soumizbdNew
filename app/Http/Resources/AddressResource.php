<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AddressResource extends JsonResource
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
            'id'           => $this->id,
            'userId'       => $this->user_id,
            'countryId'    => $this->country_id,
            'country'      => $this->country,
            'stateId'      => $this->state_id,
            'state'        => $this->state,
            'cityId'       => $this->city_id,
            'city'         => $this->city,
            'areaId'       => $this->area_id,
            'area'         => $this->area,
            'address'      => $this->address,
            'fullAddress'  => $this->address . ", " . $this->area?->name . ", " . $this->city?->name . ", " . $this->state?->name . ", " . $this->country?->name,
            'type'         => $this->type,
            'direction'    => $this->direction,
            'postalCode'   => $this->postal_code,
            'isDefault'    => $this->is_default,
        ];
    }
}
