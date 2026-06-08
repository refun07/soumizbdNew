<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingClass extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'shop_id', 'description', 'is_active'];

    public function scopeShop($query)
    {
        return $query->where('shop_id', shopId());
    }
    public  function scopeIsActive($query)
    {
        return $query->where('is_active', 1);
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'shipping_class_id');
    }

    public function shippingCharges()
    {
        return $this->hasMany(ZoneShippingCharge::class);
    }
}
