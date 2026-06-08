<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockAdjustmentProductVariation extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function stockAdjustment()
    {
        return $this->belongsTo(StockAdjustment::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class)->withTrashed();
    }

    public function productVariation()
    {
        return $this->belongsTo(ProductVariation::class)->withTrashed();
    }
}
