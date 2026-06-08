<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseReturnOrderProductVariation extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
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
