<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReturnRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'shop_id',
        'customer_id',
        'order_id',
        'invoice_no',
        'refund_amount',
        'request_type',
        'status',
        'requested_by',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function scopeShop($query)
    {
        return $query->where('shop_id', shopId());
    }

    public function items()
    {
        return $this->hasMany(ReturnRequestItem::class)->with('productVariation.product');
    }
}
