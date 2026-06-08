<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShopPayment extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function shopInfo()
    {
        return $this->belongsTo(Shop::class, 'shop_id');
    }

    public function scopeShop($query)
    {
        return $query->where('shop_id', shopId());
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function scopeNotPaid($query)
    {
        return $query->where('status', '!=', 'paid');
    }

    public function scopeRequested($query)
    {
        return $query->where('status', 'requested');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }
}
