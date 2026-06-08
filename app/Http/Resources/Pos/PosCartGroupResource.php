<?php

namespace App\Http\Resources\Pos;

use App\Http\Resources\ProductVariationResource;
use Illuminate\Http\Resources\Json\JsonResource;

class PosCartGroupResource extends JsonResource
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
            "id"                    => (int) $this->id,
            "posCartGroupId"        => (int) $this->id,
            'onHold'                => (int) $this->on_hold,
            'createdAt'             => date('d M, Y g:i:s A', strtotime($this->created_at)),
            'posCarts'              => PosCartResource::collection($this->posCarts)
        ];
    }
}
