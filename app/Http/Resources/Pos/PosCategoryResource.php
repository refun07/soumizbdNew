<?php

namespace App\Http\Resources\Pos;

use Illuminate\Http\Resources\Json\JsonResource;

class PosCategoryResource extends JsonResource
{
    public function toArray($request)
    {
        $data = [
            'id'            => (int) $this->id,
            'name'          => $this->collectTranslation('name'),
        ];

        return $data;
    }
}
