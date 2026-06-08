<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderGroup extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (OrderGroup::first() == null) {
                $model->code = getSetting('orderCodeStartsFrom') != null ? (int) getSetting('orderCodeStartsFrom') : 10001;
            } else {
                $max = (int) OrderGroup::max('code');

                if (getSetting('orderCodeStartsFrom') && $max < getSetting('orderCodeStartsFrom')) {
                    $model->code = getSetting('orderCodeStartsFrom');
                } else {
                    $model->code = $max + 1;
                }
            }
        });
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function shippingAddress()
    {
        return $this->belongsTo(UserAddress::class, 'shipping_address_id', 'id')->withTrashed();
    }

    public function billingAddress()
    {
        return $this->belongsTo(UserAddress::class, 'billing_address_id', 'id')->withTrashed();
    }

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }
    public function getTotalQuantity(): int
    {
        return $this->orders->flatMap->orderItems->sum('qty');
    }
}
