<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function scopeIsApproved($query)
    {
        return $query->where('is_approved', 1);
    }

    public function scopeIsPublished($query)
    {
        return $query->where('is_published', 1);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function warehouses()
    {
        return $this->hasMany(Warehouse::class);
    }

    public function shopSections()
    {
        return $this->hasMany(ShopSection::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function productReviews()
    {
        return $this->hasMany(ProductReview::class);
    }

    public function impressions()
    {
        return $this->hasMany(ShopImpression::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function commissionHistories()
    {
        return $this->hasMany(CommissionHistory::class);
    }

    public function shopPayments()
    {
        return $this->hasMany(ShopPayment::class);
    }
}
