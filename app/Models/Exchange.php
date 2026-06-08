<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Exchange extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'original_order_id',
        'exchange_order_id',
        'shop_id',
        'customer_id',
        'status',
        'total_original_price',
        'total_exchange_price',
        'price_difference',
        'refund_amount',
        'shipping_charge',
        'requested_by',
        'created_by',
    ];

    public function originalOrder()
    {
        return $this->belongsTo(Order::class, 'original_order_id');
    }

    public function exchangeOrder()
    {
        return $this->belongsTo(Order::class, 'exchange_order_id');
    }

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function items()
    {
        return $this->hasMany(ExchangeItem::class);
    }

    public function scopeForShop($query)
    {
        return $query->where('shop_id', shopId());
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeProcessing($query)
    {
        return $query->where('status', 'processing');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }
}
