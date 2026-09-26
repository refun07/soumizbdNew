<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'pre_order_info' => 'array',
    ];

    public function productVariation()
    {
        return $this->belongsTo(ProductVariation::class)->withTrashed();
    }

    public function product()
    {
        return $this->hasOneThrough(
            Product::class,
            ProductVariation::class,
            'product_id',
            'id',
            'product_variation_id',
            'id'
        );
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function isPreOrder(): bool
    {
        return $this->pre_order_info !== null && ($this->pre_order_info['is_pre_order'] ?? false);
    }

    public function getExpectedDate(): ?string
    {
        return $this->pre_order_info['expected_date'] ?? null;
    }

    public function getDepositPaid(): float
    {
        return $this->pre_order_info['deposit_paid'] ?? 0;
    }

    /**
     * Product line discount for this item.
     * Calculated as: (original unit price - discounted unit price) × qty.
     * This is the product sale/campaign discount only — does NOT include coupon discount.
     * Coupon discount is tracked separately at the order level (orders.coupon_discount_amount).
     */
    public function getLineDiscount(): float
    {
        return (float) $this->total_discount;
    }

    /**
     * Calculate and set the product line discount for this item.
     * Discount = (original unit price - discounted unit price) × qty.
     * This is the product sale/campaign discount only — does NOT include coupon discount.
     * Coupon discount belongs at the order level (orders.coupon_discount_amount).
     */
    public function setLineDiscount(int $productVariationId, int $qty): void
    {
        $variation = ProductVariation::with(['product.taxes.tax', 'campaignProducts.campaign'])
            ->find($productVariationId);

        if (! $variation || ! $variation->product) {
            $this->total_discount = 0;

            return;
        }

        $unitPrice = variationPrice($variation->product, $variation, false);
        $discountedUnitPrice = variationDiscountedPrice($variation->product, $variation, false);

        $this->total_discount = max(0, ($unitPrice - $discountedUnitPrice) * $qty);
    }
}
