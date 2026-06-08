<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebsiteLanding extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'seo_data' => 'array',
        'page_configs' => 'array',
        'selected_products' => 'array',
    ];

    /**
     * Get the sections for the landing page.
     */
    public function sections()
    {
        return $this->hasMany(WebsiteLandingSection::class, 'landing_id')->orderBy('order');
    }

    /**
     * Scope a query to only include published landing pages.
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    /**
     * Get the view URL attribute.
     */
    public function getViewUrlAttribute()
    {
        return '/landing/' . $this->slug;
    }

    /**
     * Get the selected products.
     */
    public function getSelectedProductsAttribute()
    {
        return $this->selected_products ?? [];
    }

    /**
     * Get the products for the landing page.
     */
    public function products()
    {
        return Product::whereIn('id', $this->selected_products ?? [])->get();
    }

    /**
     * Get the coupon for the landing page.
     */
    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }
}
