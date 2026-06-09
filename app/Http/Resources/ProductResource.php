<?php

namespace App\Http\Resources;

use App\Models\FeatureFlag;
use App\Models\Product;
use App\Traits\CategoryTrait;
use Illuminate\Http\Resources\Json\JsonResource;
use Route;


class ProductResource extends JsonResource
{
    private function getSizeChartTable(): ?array
    {
        $sizeChart = $this->size_guides;

        if (!is_array($sizeChart) || empty($sizeChart['columns']) || !is_array($sizeChart['columns'])) {
            return null;
        }

        $columnCount = count($sizeChart['columns']);
        $rows = [];

        foreach ($sizeChart['rows'] ?? [] as $row) {
            if (!is_array($row)) {
                continue;
            }

            $normalizedRow = [];
            for ($index = 0; $index < $columnCount; $index++) {
                $normalizedRow[] = (string) ($row[$index] ?? '');
            }

            $rows[] = $normalizedRow;
        }

        return [
            'columns' => array_values(array_map(fn($column) => (string) $column, $sizeChart['columns'])),
            'rows' => $rows,
        ];
    }

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
        if ($this->thumbnail_image) {
            $thumbnailImage = uploadedAsset($this->thumbnail_image);
            if ($thumbnailImage) {
                $images[] = $thumbnailImage;
            }
        }

        if ($this->gallery_images) {
            $galleryImages = array_filter(array_map('trim', explode(',', $this->gallery_images)));

            foreach ($galleryImages as $image) {
                $galleryImage = uploadedAsset($image);
                if ($galleryImage) {
                    $images[] = $galleryImage;
                }
            }
        }
        $images = array_values(array_unique($images));

        $sizeChartEnabled = getSetting('productSettingSizeChartEnabled', '1') !== '0';
        $videoLinkEnabled = getSetting('productSettingVideoLinkEnabled', '1') !== '0';
        $badgesEnabled = getSetting('productSettingBadgesEnabled', '1') !== '0';
        $tagsEnabled = getSetting('productSettingTagsEnabled', '1') !== '0';
        $estimatedDeliveryTimeEnabled = getSetting('productSettingEstimatedDeliveryTimeEnabled', '1') !== '0';
        $emiEnabled = getSetting('productSettingEmiEnabled', '1') !== '0';
        $warrantyEnabled = getSetting('productSettingWarrantyEnabled', '1') !== '0';
        $preOrderEnabled = getSetting('productSettingPreOrderEnabled', '1') !== '0';
        $relatedProductsSidebarEnabled = getSetting('productSettingRelatedProductsSidebarEnabled', '1') !== '0';
        $relatedProductsBottomEnabled = getSetting('productSettingRelatedProductsBottomEnabled', '1') !== '0';
        $maxPurchaseQty = getSetting('productSettingMaxPurchaseQtyEnabled', '1') !== '0'
            ? max(1, (int) ($this->max_purchase_qty ?? 100))
            : 100;

        $data = [
            'id'            => (int) $this->id,
            'name'          => $this->name,
            'slug'          => $this->slug,
            'thumbnailImg'  => uploadedAsset($this->thumbnail_image),
            'basePrice'     => (float) $this->__getMinBasePrice(),
            'discountedBasePrice' => (float) $this->__getMinDiscountedPrice(),
            'unit'          => $this->unit ? $this->unit->name : '',
            'stockQty'      => $this->__getStock($this),
            'hasVariation'  => (int) $this->has_variation,
            'rating'        => getProductRatings($this->productReviews()),
            'inWishlist'    => $inWishlist,
            'shop_id'       => $this->shop_id,
            'shipping_class_id' => $this->shipping_class_id,
            'badges'        => $badgesEnabled ? ProductBadgeResource::collection($this->productBadges) : [],
            'categories'    => CategoryResource::collection($this->whenLoaded('categories')),
            'shop'          => $this->shopInfo ? [
                'id' => $this->shopInfo->id,
                'name' => $this->shopInfo->name,
                'slug' => $this->shopInfo->slug
            ] : null,
            'isPreOrder'    => $preOrderEnabled && ($this->isPreOrderActive() || ($this->variations && $this->variations->contains(function($v) {
                return $v->isPreOrderEnabled();
            }))),
            'preOrderData'  => $preOrderEnabled ? $this->pre_order : null,
            'hasAvailableStock' => $this->hasAvailableStock(),
            'maxPurchaseQty' => $maxPurchaseQty,
        ];

        $additionalData = [];
        if (Route::currentRouteName() == 'api.products.show') {
            $categories = $this->categories;

            // 3 related products
            $relatedProducts = collect();
            if ($categories && count($categories) > 0) {
                $categoryIds = $categories->pluck('id');
                $relatedProducts = Product::whereHas('productCategories', function ($q) use ($categoryIds) {
                    $q->whereIn('category_id', $categoryIds)->whereNot('product_id', $this->id);
                })->take(3)->get();
            }

            // valid promoCode
            $coupon = $this->coupons()->where('start_date', '<=', strtotime(date('d-m-Y H:i:s')))->where('end_date', '>=', strtotime(date('d-m-Y H:i:s')))->first();

            // root parent category
            $rootCategory = null;
            foreach ($categories as $category) {
                $tempRoot = CategoryTrait::getRootParentCategory($category);
                if ($tempRoot != null) {
                    $rootCategory = $tempRoot;
                }
                if ($category == end($categories) && $rootCategory == null) {
                    $rootCategory = $category;
                }
            }

            $additionalData = [
                'categories'                => CategoryResource::collection($categories),
                'deliveryHours'             => $estimatedDeliveryTimeEnabled && $this->est_delivery_time ? $this->est_delivery_time : '',
                'codAvailable'              => getSetting('cod_activation') == 1,
                'hasEmi'                    => $emiEnabled ? $this->has_emi : 0,
                'emiInfo'                   => $emiEnabled ? ($this->emi_info ?: '') : '',
                'hasWarranty'               => $warrantyEnabled ? $this->has_warranty : 0,
                'warrantyInfo'              => $warrantyEnabled ? ($this->warranty_info ?: '') : '',
                'shop'                      => new ShopResource($this->shopInfo),
                'description'               => $this->collectTranslation('description'),
                'images'                    => $images,
                'videoLink'                 => $videoLinkEnabled ? ($this->video_link ?: '') : '',
                'shipping_class_id'         => $this->shipping_class_id,
                'brand'                     => $this->brand ? new BrandResource($this->brand) : null,
                'variations'                => ProductVariationResource::collection($this->variations),
                'variationCombinations'     => generateVariationCombinations($this->variationCombinations, $this),
                'relatedProducts'           => RelatedProductResource::collection($relatedProducts),
                'relatedProductsSidebarEnabled' => $relatedProductsSidebarEnabled,
                'relatedProductsBottomEnabled' => $relatedProductsBottomEnabled,
                'promoCode'                 => $coupon ?  new CouponResource($coupon) : null,
                'rootCategory'              => $rootCategory ? new CategoryResource($rootCategory) : null,
                'tags'                      => $tagsEnabled ? ProductTagResource::collection($this->tags) : [],
                'hasSizeChart'              => $sizeChartEnabled && (bool) $this->isSizeChart,
                'sizeChartImage'            => $sizeChartEnabled && $this->isSizeChart && $this->chart_image ? uploadedAsset($this->chart_image) : null,
                'sizeChartTable'            => $sizeChartEnabled && $this->isSizeChart ? $this->getSizeChartTable() : null,
                'color_image_enabled'       => FeatureFlag::isEnabled('color_image_enabled'),
            ];
        }

        $data = array_merge($data, $additionalData);
        return $data;
    }

    # get stock -- to check outOfStock as the stock is based on warehouses of selected zone
    private function __getStock($product)
    {
        $stockQty = 0;

        // if product has variations
        if ($product->has_variation == 1) {
            $productVariations = $product->variations;
            foreach ($productVariations as $productVariation) {
                $stockQty += (int) $productVariation?->productVariationStocks()->whereIn('warehouse_id', session('WarehouseIds') ?? [1])->sum('stock_qty');
            }
        } else {
            // if product does not have any variations
            $productVariation   = $product->variations()->first();
            $stockQty           = $productVariation?->productVariationStocks()->whereIn('warehouse_id', session('WarehouseIds') ?? [1])->sum('stock_qty');
        }

        return (int) $stockQty;
    }

    # get minimum base price across all variations
    private function __getMinBasePrice()
    {
        $minPrice = null;

        if ($this->has_variation == 1) {
            $productVariations = $this->variations;
            foreach ($productVariations as $productVariation) {
                $price = variationPrice($this, $productVariation, false);
                if ($minPrice === null || $price < $minPrice) {
                    $minPrice = $price;
                }
            }
        } else {
            $productVariation = $this->variations()->first();
            if ($productVariation) {
                $minPrice = variationPrice($this, $productVariation, false);
            }
        }

        if ($minPrice === null) {
            return productBasePrice($this, false);
        }

        return $minPrice;
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
