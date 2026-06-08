<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class TopCategoryResource extends JsonResource
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
            'totalSaleCount'    => (int) $this->total_sale_count,
            'totalSaleAmount'   => (float) $this->total_sale_amount,
        ];
    }
}
