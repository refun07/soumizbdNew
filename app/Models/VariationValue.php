<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VariationValue extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    protected $with = ['variationValueTranslations'];

    public function scopeIsActive($query)
    {
        return $query->where('is_active', 1);
    }

    public function collectTranslation($entity = '', $lang_key = '')
    {
        $lang_key = $lang_key ==  '' ? app()->getLocale() : $lang_key;
        $translations = $this->variationValueTranslations->where('lang_key', $lang_key)->first();
        return $translations != null && $translations->$entity ? $translations->$entity : $this->$entity;
    }

    public function variationValueTranslations()
    {
        return $this->hasMany(VariationValueTranslation::class);
    }

    public function variation()
    {
        return $this->belongsTo(Variation::class);
    }
}
