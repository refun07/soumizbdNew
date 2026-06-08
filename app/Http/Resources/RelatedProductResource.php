<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Warehouse;

class RelatedProductResource extends JsonResource
{
    public function toArray($request)
    {
        $user       = apiUser();
        $wishlists  = [];
        $inWishlist = false;
        if ($user != null) {
            $wishlists = $user->wishlists()->pluck('product_id')->toArray();
            if (in_array($this->id, $wishlists)) {
                $inWishlist = true;
            }
        }

        $images = [];
        if (!is_null($this->gallery_images)) {
            $galleryImages = explode(',', $this->gallery_images);

            foreach ($galleryImages as $image) {
                array_push($images, uploadedAsset($image));
            }
        }

        $data = [
            'id'            => (int) $this->id,
            'name'          => $this->name,
            'slug'          => $this->slug,
            'thumbnailImg'  => uploadedAsset($this->thumbnail_image),
            'basePrice'     => (float) productBasePrice($this, false),
            'discountedBasePrice' => (float) $this->__getMinDiscountedPrice(),
            'unit'          => $this->unit ? $this->unit->name : '',
            'stockQty'      => $this->__getStock($this),
            'hasVariation'  => (int) $this->has_variation,
            'rating'        => getProductRatings($this->productReviews()),
            'inWishlist'    => $inWishlist,
            'badges'        => ProductBadgeResource::collection($this->productBadges),
            'hasAvailableStock' => $this->hasAvailableStock()
        ];

        $additionalData = [];
        $data = array_merge($data, $additionalData);
        return $data;
    }

    # get stock -- to check outOfStock as the stock is based on warehouses of selected zone
    private function __getStock($product)
    {
        $warehouseIds = Warehouse::pluck('id')->toArray();
        $stockQty = 0;

        // if product has variations
        if ($product->has_variation == 1) {
            $productVariations = $product->variations;
            foreach ($productVariations as $productVariation) {
                $stockQty += (int) $productVariation?->productVariationStocks()->whereIn('warehouse_id', $warehouseIds)->sum('stock_qty');
            }
        } else {
            // if product does not have any variations
            $productVariation   = $product->variations()->first();
            $stockQty           = $productVariation?->productVariationStocks()->whereIn('warehouse_id', $warehouseIds)->sum('stock_qty');
        }

        return (int) $stockQty;
    }

    # check if any variation has stock (uses all warehouses)
    private function __hasAnyVariationWithStock(): bool
    {
        $warehouseIds = Warehouse::pluck('id')->toArray();

        if ($this->has_variation != 1) {
            return $this->stock_qty > 0;
        }

        foreach ($this->variations as $variation) {
            $stockQty = $variation->productVariationStocks()
                ->whereIn('warehouse_id', $warehouseIds)
                ->sum('stock_qty');
            if ($stockQty > 0) {
                return true;
            }
        }

        return false;
    }

    # get minimum discounted price across all variations
    private function __getMinDiscountedPrice()
    {
        $minDiscountedPrice = null;

        // if product has variations, find minimum discounted price
        if ($this->has_variation == 1) {
            $productVariations = $this->variations;
            foreach ($productVariations as $productVariation) {
                $discountedPrice = variationDiscountedPrice($this, $productVariation, false);
                if ($minDiscountedPrice === null || $discountedPrice < $minDiscountedPrice) {
                    $minDiscountedPrice = $discountedPrice;
                }
            }
        } else {
            // if product does not have any variations
            $productVariation = $this->variations()->first();
            if ($productVariation) {
                $minDiscountedPrice = variationDiscountedPrice($this, $productVariation, false);
            }
        }

        // If no discount is applicable, return base price
        if ($minDiscountedPrice === null) {
            return productBasePrice($this, false);
        }

        return $minDiscountedPrice;
    }
}
