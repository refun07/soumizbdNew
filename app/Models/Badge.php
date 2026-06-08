<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Badge extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    protected $with = ['badgeTranslations'];

    public function collectTranslation($entity = '', $lang_key = '')
    {
        $lang_key = $lang_key ==  '' ? app()->getLocale() : $lang_key;
        $translations = $this->badgeTranslations->where('lang_key', $lang_key)->first();
        return $translations != null && $translations->$entity ? $translations->$entity : $this->$entity;
    }

    public  function scopeIsActive($query)
    {
        return $query->where('is_active', 1);
    }

    public function badgeTranslations()
    {
        return $this->hasMany(BadgeTranslation::class);
    }
}
