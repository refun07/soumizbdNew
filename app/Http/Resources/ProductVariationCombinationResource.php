<?php

namespace App\Http\Resources;

use App\Models\Variation;
use App\Models\VariationValue;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductVariationCombinationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $variation = Variation::withTrashed()->find($this->variation_id);
        $variationValue = VariationValue::withTrashed()->find($this->variation_value_id);

        return [
            'id'                    => (int) $this->id,
            'variationId'           => (int) $this->variation_id,
            'variationName'         => $variation?->collectTranslation('name') ?? '',
            'variationValueId'      => (int) $this->variation_value_id,
            'variationValueName'    => $variationValue?->collectTranslation('name') ?? '',
            'name'                  => $variationValue?->collectTranslation('name') ?? '',
        ];
    }
}
