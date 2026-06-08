<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    use HasFactory;

    public function scopeIsPublished($query)
    {
        return $query->where('is_published', 1);
    }

    public function scopeShop($query)
    {
        return $query->where('shop_id', shopId());
    }

    public function shopInfo()
    {
        return $this->belongsTo(Shop::class, 'shop_id');
    }

    public function campaignProducts()
    {
        return $this->hasMany(CampaignProduct::class);
    }
}
