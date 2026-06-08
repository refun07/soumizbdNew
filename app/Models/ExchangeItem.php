<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExchangeItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'exchange_id',
        'original_product_variation_id',
        'exchange_product_variation_id',
        'original_order_item_id',
        'exchange_order_item_id',
        'quantity',
        'original_quantity',
        'original_unit_price',
        'exchange_unit_price',
        'price_difference',
        'reason',
        'note',
        'status',
        'media_path',
    ];

    public function exchange()
    {
        return $this->belongsTo(Exchange::class);
    }

    public function originalVariation()
    {
        return $this->belongsTo(ProductVariation::class, 'original_product_variation_id');
    }

    public function exchangeVariation()
    {
        return $this->belongsTo(ProductVariation::class, 'exchange_product_variation_id');
    }

    public function originalOrderItem()
    {
        return $this->belongsTo(OrderItem::class, 'original_order_item_id');
    }

    public function exchangeOrderItem()
    {
        return $this->belongsTo(OrderItem::class, 'exchange_order_item_id');
    }
}
