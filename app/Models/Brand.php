<?php

namespace App\Models;

use App\Traits\CreatedUpdatedDeletedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Brand extends Model
{
    use HasFactory, SoftDeletes, CreatedUpdatedDeletedBy;

    protected $guarded = [];

    protected $with = ['brandTranslations'];

    public function scopeIsActive($query)
    {
        return $query->where('is_active', 1);
    }

    public function collectTranslation($entity = '', $lang_key = '')
    {
        $lang_key = $lang_key ==  '' ? app()->getLocale() : $lang_key;
        $translations = $this->brandTranslations->where('lang_key', $lang_key)->first();
        return $translations != null && $translations->$entity ? $translations->$entity : $this->$entity;
    }

    public function brandTranslations()
    {
        return $this->hasMany(BrandTranslation::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
