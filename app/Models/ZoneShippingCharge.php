<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ZoneShippingCharge extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'charge' => 'float',
        'mrate' => 'integer',
    ];

    public function zone()
    {
        return $this->belongsTo(Zone::class);
    }

    public function shippingClass()
    {
        return $this->belongsTo(ShippingClass::class);
    }

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }
}
