<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WarehouseZone extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function scopeShop($query)
    {
        return $query->where('shop_id', shopId());
    }

    public function warehouses()
    {
        return $this->belongsToMany(Warehouse::class, 'warehouse_zone', 'zone_id', 'warehouse_id');
    }
}
