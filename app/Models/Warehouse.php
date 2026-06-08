<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Warehouse extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public function scopeShop($query)
    {
        return $query->where('shop_id', shopId());
    }

    public function zones()
    {
        return $this->belongsToMany(Zone::class, 'warehouse_zones', 'warehouse_id', 'zone_id');
    }

    public function warehouseZones()
    {
        return $this->hasMany(WarehouseZone::class);
    }
}
