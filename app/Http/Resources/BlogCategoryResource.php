<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BlogCategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = [
            'id'        => (int) $this->id,
            'name'      => $this->collectTranslation('name'),
            'createdAt' => $this->created_at ? date('d M, Y', strtotime($this->created_at)) : null,
        ];

        return $data;
    }
}
