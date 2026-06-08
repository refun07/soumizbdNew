<?php

namespace App\Models;

use App\Traits\CreatedUpdatedDeletedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Unit extends Model
{
    use HasFactory, SoftDeletes, CreatedUpdatedDeletedBy;

    protected $guarded = [];

    protected $with = ['unitTranslations'];

    public function scopeIsActive($query)
    {
        return $query->where('is_active', 1);
    }

    public function collectTranslation($entity = '', $lang_key = '')
    {
        $lang_key = $lang_key ==  '' ? app()->getLocale() : $lang_key;
        $translations = $this->unitTranslations->where('lang_key', $lang_key)->first();
        return $translations != null && $translations->$entity ? $translations->$entity : $this->$entity;
    }

    public function unitTranslations()
    {
        return $this->hasMany(UnitTranslation::class);
    }
}
