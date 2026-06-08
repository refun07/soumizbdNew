<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockAdjustment extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function scopeShop($query)
    {
        return $query->where('shop_id', shopId());
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function productVariations()
    {
        return $this->hasMany(StockAdjustmentProductVariation::class);
    }
}
