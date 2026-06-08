<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderOtp extends Model
{
    protected $table = 'order_otps';

    protected $fillable = [
        'phone',
        'otp',
        'expires_at',
    ];

    protected $dates = ['expires_at'];

    public $timestamps = true;
}
