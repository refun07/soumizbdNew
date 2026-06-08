<?php

namespace App\Http\Resources\Pos;

use Illuminate\Http\Resources\Json\JsonResource;

class PosProductResource extends JsonResource
{
    public function toArray($request)
    {
        $data = [
            'id'            => (int) $this->id,
            'name'          => $this->collectTranslation('name'),
            'thumbnailImg'  => uploadedAsset($this->thumbnail_image),
        ];

        return $data;
    }
}
