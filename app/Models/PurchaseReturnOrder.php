<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseReturnOrder extends Model
{
    use HasFactory;
    protected $guarded = [];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (PurchaseReturnOrder::first() == null) {
                $model->reference_code = 1;
            } else {
                $model->reference_code = PurchaseReturnOrder::max('reference_code') + 1;
            }
        });
    }

    public function scopeShop($query)
    {
        return $query->where('shop_id', shop()->id);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function orders()
    {
        return $this->hasMany(PurchaseReturnOrderProductVariation::class);
    }

    public function payments()
    {
        return $this->morphMany(PurchaseOrderPayment::class, 'payable');
    }
}
