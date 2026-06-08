<?php

namespace App\Http\Resources\Pos;

use App\Http\Resources\ProductVariationCombinationResource;
use App\Http\Resources\ProductVariationStockResource;
use Illuminate\Http\Resources\Json\JsonResource;

class PosProductVariationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $availableStock = (int) $this->productVariationStocks->sum('stock_qty');

        return [
            'id'                            => $this->id,
            'productId'                     => (int) $this->product_id,
            'product'                       => new PosProductResource($this->product),
            'name'                          => generateVariationName($this->code),
            'code'                          => $this->code,
            'sku'                           => $this->sku,
            'stocks'                        => ProductVariationStockResource::collection($this->productVariationStocks),
            'availableStock'                => $availableStock,
            'combinations'                  => ProductVariationCombinationResource::collection($this->combinations()->withoutTrashed()->get()),
            'image'                         => uploadedAsset($this->image),
            'basePrice'                     => (float) variationPrice($this->product, $this, false),
            'discountedBasePrice'           => (float) variationDiscountedPrice($this->product, $this, false),
            'basePriceWithTax'              => (float) variationPrice($this->product, $this),
            'discountedBasePriceWithTax'    => (float) variationDiscountedPrice($this->product, $this),
            'tax'                           => (float) variationDiscountedPrice($this->product, $this) - (float) variationDiscountedPrice($this->product, $this, false),
        ];
    }
}
