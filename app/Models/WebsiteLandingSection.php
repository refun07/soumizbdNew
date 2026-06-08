<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebsiteLandingSection extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'section_data' => 'array',
        'responsive_data' => 'array',
    ];

    /**
     * Get the landing page that owns the section.
     */
    public function landing()
    {
        return $this->belongsTo(WebsiteLanding::class);
    }

    /**
     * Scope a query to only include visible sections.
     */
    public function scopeVisible($query)
    {
        return $query->where('is_visible', true);
    }
}
