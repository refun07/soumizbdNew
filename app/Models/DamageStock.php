<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DamageStock extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'shop_id',
        'product_variation_id',
        'warehouse_id',
        'quantity',
        'damage_type',
        'reason',
        'notes',
        'return_request_id',
        'return_date',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    public function scopeShop($query)
    {
        return $query->where('shop_id', shopId());
    }

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function productVariation()
    {
        return $this->belongsTo(ProductVariation::class)->withTrashed();
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function returnRequest()
    {
        return $this->belongsTo(ReturnRequest::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
