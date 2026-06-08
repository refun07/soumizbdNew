<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Coupon extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public function scopeIsActive($query)
    {
        return $query->where('is_active', 1);
    }

    public function scopeShop($query)
    {
        return $query->where('shop_id', shopId());
    }

    public function shopInfo()
    {
        return $this->belongsTo(Shop::class, 'shop_id');
    }

    public function couponUsages()
    {
        return $this->hasMany(CouponUsage::class);
    }

    public function conditions()
    {
        return $this->hasMany(CouponCondition::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'coupon_categories', 'coupon_id', 'category_id');
    }

    public function couponCategories()
    {
        return $this->hasMany(CouponCategory::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'coupon_products', 'coupon_id', 'product_id');
    }

    public function couponProducts()
    {
        return $this->hasMany(CouponProduct::class);
    }
}
