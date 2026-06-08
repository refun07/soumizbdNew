<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReturnRequestItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'return_request_id',
        'product_variation_id',
        'product_id',
        'quantity',
        'price',
        'reason',
        'note',
        'status',
        'is_paid',
        'media_path'
    ];

    public function returnRequest()
    {
        return $this->belongsTo(ReturnRequest::class);
    }

    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function productVariation()
    {
        return $this->belongsTo(ProductVariation::class);
    }
}
