<?php

namespace App\Http\Resources\Campaign;

use Illuminate\Http\Resources\Json\JsonResource;

class CampaignProductResource extends JsonResource
{
    public function toArray($request)
    {
        $variations = $this->variations()->withoutTrashed()->get();
        $blockedVariationIds = $request->attributes->get('blockedVariationIds', []);
        $isCampaignProduct = $variations->contains(function ($variation) use ($blockedVariationIds) {
            return in_array((int) $variation->id, $blockedVariationIds, true);
        });

        $data = [
            'id'                        => (int) $this->id,
            'name'                      => $this->name,
            'thumbnailImage'            => uploadedAsset($this->thumbnail_image),
            'basePrice'                 => (float) productBasePrice($this, false),
            'hasVariation'              => (int) $this->has_variation,
            'isCampaignProduct'         => $isCampaignProduct,
            'variations'                => CampaignProductVariationResource::collection($variations)
        ];
        return $data;
    }
}
