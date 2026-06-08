<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseOrder extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (PurchaseOrder::first() == null) {
                $model->reference_code = 1;
            } else {
                $model->reference_code = PurchaseOrder::max('reference_code') + 1;
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

    public function orders()
    {
        return $this->hasMany(PurchaseOrderProductVariation::class);
    }

    public function return()
    {
        return $this->hasOne(PurchaseReturnOrder::class);
    }

    public function payments()
    {
        return $this->morphMany(PurchaseOrderPayment::class, 'payable');
    }
}
