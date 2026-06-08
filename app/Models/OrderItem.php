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
}
