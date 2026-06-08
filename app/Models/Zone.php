<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Zone extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];


    public function scopeIsActive($query)
    {
        return $query->where('is_active', 1);
    }

    public function shippingCharge($shopId)
    {
        return $this->hasOne(ZoneShippingCharge::class)->where('shop_id', $shopId)->first();
    }

    public function shippingCharges()
    {
        return $this->hasMany(ZoneShippingCharge::class);
    }

    public function warehouseZones($shopId)
    {
        return $this->hasOne(WarehouseZone::class)->where('shop_id', $shopId)->first();
    }

    public function shippingChargeByClass($classId, $shopId = null)
    {
        return $this->shippingCharges()
            ->where('shipping_class_id', $classId)
            ->when($shopId, fn($q) => $q->where('shop_id', $shopId))
            ->first();
    }
}
