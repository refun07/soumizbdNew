<?php

namespace App\Models;

use App\Traits\CreatedUpdatedDeletedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasFactory, SoftDeletes, CreatedUpdatedDeletedBy;

    protected $guarded = [];

    protected $with = ['categoryTranslations', 'parentCategory'];

    public function scopeIsRoot($query)
    {
        return $query->where('level', 0);
    }

    public function collectTranslation($entity = '', $lang_key = '')
    {
        $lang_key = $lang_key ==  '' ? app()->getLocale() : $lang_key;
        $translations = $this->categoryTranslations->where('lang_key', $lang_key)->first();
        return $translations != null && $translations->$entity ? $translations->$entity : $this->$entity;
    }

    public function categoryTranslations()
    {
        return $this->hasMany(CategoryTranslation::class);
    }

    public function childrenCategories()
    {
        return $this->hasMany(Category::class, 'parent_id')->with('childrenCategories');
    }

    public function parentCategory()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function productCategories()
    {
        return $this->hasMany(ProductCategory::class);
    }

    public function variations()
    {
        return $this->belongsToMany(Variation::class, 'category_variations', 'category_id', 'variation_id');
    }
}
