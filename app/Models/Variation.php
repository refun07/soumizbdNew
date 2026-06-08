<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Variation extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    protected $with = ['variationTranslations'];

    public function scopeIsActive($query)
    {
        return $query->where('is_active', 1);
    }

    public function scopeIsColor($query)
    {
        return $query->where('is_color', 1);
    }

    public function collectTranslation($entity = '', $lang_key = '')
    {
        $lang_key = $lang_key ==  '' ? app()->getLocale() : $lang_key;
        $translations = $this->variationTranslations->where('lang_key', $lang_key)->first();
        return $translations != null && $translations->$entity ? $translations->$entity : $this->$entity;
    }

    public function variationTranslations()
    {
        return $this->hasMany(VariationTranslation::class);
    }

    public function variationValues()
    {
        return $this->hasMany(VariationValue::class);
    }

    public function getLimitedVariationValues()
    {
        return $this->variationValues()->latest()->take(5);
    }

    public function cartItems()
    {
        return $this->hasMany(Cart::class);
    }
}
