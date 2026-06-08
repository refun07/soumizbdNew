<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductVariationCombination extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public function getImageUrlAttribute(): ?string
    {
        if ($this->image) {
            return uploadedAsset($this->image);
        }
        return null;
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function productVariation()
    {
        return $this->belongsTo(ProductVariation::class)->withTrashed();
    }

    public function variation()
    {
        return $this->belongsTo(Variation::class);
    }

    public function variationValue()
    {
        return $this->belongsTo(VariationValue::class);
    }
}
