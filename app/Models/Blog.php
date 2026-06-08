<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    use HasFactory;

    protected $guarded  = [];

    protected $with     = ['blogTranslations'];

    public function scopeIsPublished($query)
    {
        return $query->where('is_published', 1);
    }

    public function blogTranslations()
    {
        return $this->hasMany(BlogTranslation::class);
    }

    public function collectTranslation($entity = '', $lang_key = '')
    {
        $lang_key = $lang_key ==  '' ? app()->getLocale() : $lang_key;
        $translations = $this->blogTranslations->where('lang_key', $lang_key)->first();
        return $translations != null && $translations->$entity ? $translations->$entity : $this->$entity;
    }

    public function blogCategory()
    {
        return $this->belongsTo(BlogCategory::class);
    }
}
