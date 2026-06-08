<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductVariation extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'pre_order' => 'array',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id')->withTrashed();
    }

    public function combinations()
    {
        return $this->hasMany(ProductVariationCombination::class)->withTrashed();
    }

    public function productVariationStocks()
    {
        return $this->hasMany(ProductVariationStock::class);
    }

    public function campaignProducts()
    {
        return $this->hasMany(CampaignProduct::class);
    }

    public function isPreOrderEnabled(): bool
    {
        if (getSetting('productSettingPreOrderEnabled', '1') === '0') {
            return false;
        }

        $preOrder = $this->pre_order;
        $product = $this->product;
        $productPreOrder = $product->pre_order;

        if (!$productPreOrder || !isset($productPreOrder['is_active']) || !$productPreOrder['is_active']) {
            return false;
        }

        if ($preOrder !== null && isset($preOrder['enabled']) && $preOrder['enabled'] === false) {
            return false;
        }

        $now = now();

        if (isset($productPreOrder['start_date']) && $productPreOrder['start_date'] && $now->lt($productPreOrder['start_date'])) {
            return false;
        }
        if (isset($productPreOrder['end_date']) && $productPreOrder['end_date'] && $now->gt($productPreOrder['end_date'])) {
            return false;
        }

        return true;
    }

    public function isPreOrderDisabled(): bool
    {
        $preOrder = $this->pre_order;
        if ($preOrder === null) {
            return false;
        }
        return isset($preOrder['enabled']) && $preOrder['enabled'] === false;
    }

    public function getPreOrderOrderLimit(): int
    {
        $product = $this->product;
        return (int) ($product->pre_order['order_limit'] ?? 0);
    }

    public function getPreOrderTotalLimit(): int
    {
        $product = $this->product;
        return (int) ($product->pre_order['total_order_limit'] ?? 0);
    }

    public function getCurrentOrders(): int
    {
        $stats = $this->preOrderStats;
        return $stats ? $stats->current_orders : 0;
    }

    public function isSoldOut(): bool
    {
        $totalLimit = $this->getPreOrderTotalLimit();
        if ($totalLimit <= 0) {
            return false;
        }
        return $this->getCurrentOrders() >= $totalLimit;
    }

    public function remainingOrders(): int
    {
        $totalLimit = $this->getPreOrderTotalLimit();
        if ($totalLimit <= 0) {
            return PHP_INT_MAX;
        }
        return max(0, $totalLimit - $this->getCurrentOrders());
    }

    public function getExpectedReleaseDate(): ?string
    {
        return $this->product->pre_order['expected_release_date'] ?? null;
    }

    public function getDepositAmount(): ?float
    {
        $amount = $this->product->pre_order['deposit_amount'] ?? null;
        return $amount ? (float) $amount : null;
    }

    public function getStockQuantity(?int $warehouseId = null): int
    {
        if ($warehouseId) {
            return (int) $this->productVariationStocks()
                ->where('warehouse_id', $warehouseId)
                ->sum('stock_qty');
        }

        return (int) $this->productVariationStocks()
            ->sum('stock_qty');
    }

    public function hasAvailableStock(?int $warehouseId = null): bool
    {
        if (getSetting('allowBackorder') == '1') {
            return true;
        }

        if ($this->getStockQuantity($warehouseId) > 0) {
            return true;
        }

        return $this->isPreOrderEnabled();
    }
}
