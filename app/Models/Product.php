<?php

namespace App\Models;

use App\Traits\CreatedUpdatedDeletedBy;
use Cache;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, CreatedUpdatedDeletedBy, SoftDeletes;

    protected $guarded  = [];

    protected $with     = ['productTranslations'];

    protected $casts = [
        'packaging_cost' => 'decimal:2',
        'additional_costs' => 'decimal:2',
        'pre_order' => 'array',
        'qr_data' => 'array',
        'size_guides' => 'array',
        'isSizeChart' => 'boolean',
        'hide_on_ecom' => 'boolean',
    ];

    public function scopeShop($query)
    {
        return $query->where('shop_id', shopId());
    }

    public function shopInfo()
    {
        return $this->belongsTo(Shop::class, 'shop_id');
    }

    public function scopeFromPublishedShops($query)
    {
        if (config('app.app_mode') == "singleVendor") {
            return $query->where('shop_id', 1);
        } else {
            # publishedShopsIds cache must be reset on shop status changes 
            $publishedShopsIds = Cache::rememberForever('publishedShopsIds', function () {
                return  Shop::isApproved()->isPublished()->pluck('id')->toArray();
            });
            return $query->whereIn('shop_id', $publishedShopsIds);
        }
    }

    public function scopeIsPublished($query)
    {
        return $query->where('is_published', 1);
    }

    public function scopeHideOnEcom($query)
    {
        return $query->where('hide_on_ecom', false);
    }

    public function productTranslations()
    {
        return $this->hasMany(ProductTranslation::class);
    }

    public function collectTranslation($entity = '', $lang_key = '')
    {
        $lang_key = $lang_key ==  '' ? app()->getLocale() : $lang_key;
        $translations = $this->productTranslations->where('lang_key', $lang_key)->first();
        return $translations != null && $translations->$entity ? $translations->$entity : $this->$entity;
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'product_categories', 'product_id', 'category_id');
    }

    public function productCategories()
    {
        return $this->hasMany(ProductCategory::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function collection()
    {
        return $this->belongsTo(ProductCollection::class, 'collection_id');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function variations()
    {
        return $this->hasMany(ProductVariation::class);
    }

    public function variationCombinations()
    {
        return $this->hasMany(ProductVariationCombination::class);
    }

    public function taxes()
    {
        return $this->hasMany(ProductTax::class);
    }

    public function productTaxes()
    {
        return $this->belongsToMany(Tax::class, 'product_taxes', 'product_id', 'tax_id');
    }

    public function badges()
    {
        return $this->hasMany(ProductBadge::class);
    }

    public function productBadges()
    {
        return $this->belongsToMany(Badge::class, 'product_badges', 'product_id', 'badge_id');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'product_tags', 'product_id', 'tag_id');
    }

    public function productTags()
    {
        return $this->hasMany(ProductTag::class);
    }

    public function productReviews()
    {
        return $this->hasMany(ProductReview::class, 'product_id');
    }

    public function campaignProducts()
    {
        return $this->hasMany(CampaignProduct::class);
    }

    public function coupons()
    {
        return $this->belongsToMany(Coupon::class, 'coupon_products', 'product_id', 'coupon_id');
    }

    public function couponProducts()
    {
        return $this->hasMany(CouponProduct::class);
    }

    public function shippingClass()
    {
        return $this->belongsTo(ShippingClass::class);
    }

    public function purchaseOrderProductVariations()
    {
        return $this->hasMany(PurchaseOrderProductVariation::class);
    }

    public function getAveragePurchaseCost()
    {
        $purchaseVariations = $this->purchaseOrderProductVariations()
            ->whereHas('purchaseOrder', function ($query) {
                $query->whereNull('deleted_at');
            })
            ->get();

        if ($purchaseVariations->isEmpty()) {
            return 0;
        }

        $totalCost = 0;
        $totalQuantity = 0;

        foreach ($purchaseVariations as $variation) {
            $totalCost += $variation->unit_price * $variation->qty;
            $totalQuantity += $variation->qty;
        }

        return $totalQuantity > 0 ? round($totalCost / $totalQuantity, 2) : 0;
    }

    public function preOrderStats()
    {
        return $this->hasMany(PreOrderStat::class);
    }

    public function preOrderStatsForVariation($variationId)
    {
        return $this->hasMany(PreOrderStat::class)->where('product_variation_id', $variationId);
    }

    public function getPreOrderDataAttribute($value)
    {
        return $this->pre_order ?? null;
    }

    public function isPreOrderActive(): bool
    {
        if (getSetting('productSettingPreOrderEnabled', '1') === '0') {
            return false;
        }

        $preOrder = $this->pre_order;
        if (!$preOrder || !isset($preOrder['is_active']) || !$preOrder['is_active']) {
            return false;
        }

        $now = now();
        if (isset($preOrder['start_date']) && $preOrder['start_date']) {
            $startDate = \Carbon\Carbon::createFromFormat('d-m-Y', $preOrder['start_date']);
            if ($startDate && $now->lt($startDate)) {
                return false;
            }
        }
        if (isset($preOrder['end_date']) && $preOrder['end_date']) {
            $endDate = \Carbon\Carbon::createFromFormat('d-m-Y', $preOrder['end_date']);
            if ($endDate && $now->gt($endDate)) {
                return false;
            }
        }

        return true;
    }

    public function getPreOrderLimit(): int
    {
        return $this->pre_order['order_limit'] ?? 0;
    }

    public function getPreOrderDescription(): string
    {
        return $this->pre_order['description'] ?? '';
    }

    public function getPreOrderExpectedDate(): ?string
    {
        return $this->pre_order['expected_release_date'] ?? null;
    }

    public function getPreOrderDepositAmount(): ?float
    {
        return isset($this->pre_order['deposit_amount']) ? (float) $this->pre_order['deposit_amount'] : null;
    }

    public function getTotalOrderLimit(): int
    {
        return $this->pre_order['total_order_limit'] ?? 0;
    }

    public function getStartDate(): ?string
    {
        return $this->pre_order['start_date'] ?? null;
    }

    public function getEndDate(): ?string
    {
        return $this->pre_order['end_date'] ?? null;
    }

    public function scopeIsPreOrder($query)
    {
        return $query->whereNotNull('pre_order')
            ->where('pre_order->is_active', true);
    }

    public function hasAnyVariationWithStock(): bool
    {
        if ($this->has_variation != 1) {
            return $this->stock_qty > 0;
        }

        return $this->variations->contains(function ($variation) {
            $stockQty = $variation->productVariationStocks()
                ->whereIn('warehouse_id', session('WarehouseIds') ?? [1])
                ->sum('stock_qty');
            return $stockQty > 0;
        });
    }

    /**
     * Check if product has available stock considering backorder, pre-order, and warehouse
     *
     * @param int|null $warehouseId Specific warehouse ID, null for all warehouses
     * @return bool
     */
    public function hasAvailableStock(?int $warehouseId = null): bool
    {
        // Check global backorder setting
        if (getSetting('allowBackorder') == '1') {
            return true;
        }

        $warehouseIds = $warehouseId ? [$warehouseId] : Warehouse::pluck('id')->toArray();

        // Get actual stock quantity
        $stockQty = $this->getStockQty($warehouseIds);

        // Has actual stock
        if ($stockQty > 0) {
            return true;
        }

        // Check pre-order availability
        return $this->isPreOrderAvailable();
    }

    /**
     * Get stock quantity for display/admin purposes
     *
     * @param int|null $warehouseId Specific warehouse ID, null for all warehouses
     * @return int
     */
    public function getStockQuantity(?int $warehouseId = null): int
    {
        $warehouseIds = $warehouseId ? [$warehouseId] : Warehouse::pluck('id')->toArray();
        return $this->getStockQty($warehouseIds);
    }

    /**
     * Get stock quantity across specified warehouses
     *
     * @param array $warehouseIds
     * @return int
     */
    private function getStockQty(array $warehouseIds): int
    {
        if ($this->has_variation) {
            return $this->variations->sum(function ($variation) use ($warehouseIds) {
                return $variation->productVariationStocks()
                    ->whereIn('warehouse_id', $warehouseIds)
                    ->sum('stock_qty');
            });
        }

        $variation = $this->variations()->first();
        return $variation?->productVariationStocks()
            ->whereIn('warehouse_id', $warehouseIds)
            ->sum('stock_qty') ?? 0;
    }

    /**
     * Check if product is available for pre-order
     *
     * @return bool
     */
    private function isPreOrderAvailable(): bool
    {
        if (empty($this->pre_order) || !is_array($this->pre_order)) {
            return false;
        }

        // Check if active
        if (!($this->pre_order['is_active'] ?? false)) {
            return false;
        }

        // Check if end date passed
        $endDate = $this->pre_order['end_date'] ?? null;
        if ($endDate && strtotime($endDate) < time()) {
            return false;
        }

        // Check if limit exceeded
        $orderLimit = $this->pre_order['order_limit'] ?? null;
        if ($orderLimit !== null && $this->total_sale_count >= $orderLimit) {
            return false;
        }

        return true;
    }
}
