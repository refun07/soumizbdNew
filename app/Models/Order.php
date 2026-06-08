<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id', 'agent_id', 'order_group_id', 'order_code', 'shop_id', 'warehouse_id',
        'amount', 'tax_amount', 'shipping_charge_amount', 'discount_amount',
        'coupon_discount_amount', 'total_amount', 'coupon_id', 'pickup_or_delivery',
        'delivery_status', 'payment_status', 'courier_name', 'tracking_number',
        'tracking_url', 'ip_address', 'pos_order', 'is_exchange_order',
        'original_order_id', 'source',
        'created_by', 'updated_by', 'deleted_by'
    ];

    protected $casts = [
        'is_exchange_order' => 'boolean',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (Order::first() == null) {
                $model->order_code = getSetting('orderCodeStartsFrom') != null ? (int) getSetting('orderCodeStartsFrom') : 10001;
            } else {
                $max = (int) Order::max('order_code');

                if (getSetting('orderCodeStartsFrom') && $max < getSetting('orderCodeStartsFrom')) {
                    $model->order_code = getSetting('orderCodeStartsFrom');
                } else {
                    $model->order_code = $max + 1;
                }
            }
        });
    }

    // protected $dispatchesEvents = [ 'created' => OrderPlaced::class];

    public function scopeShopOrders($query)
    {
        return $query->where('shop_id', shopId());
    }

    public function scopeIsPlaced($query)
    {
        return $query->where('delivery_status', 'order_placed');
    }

    public function scopeIsConfirmed($query)
    {
        return $query->where('delivery_status', 'confirmed');
    }

    public function scopeIsProcessing($query)
    {
        return $query->where('delivery_status', 'processing');
    }

    public function scopeIsShipped($query)
    {
        return $query->where('delivery_status', 'shipped');
    }

    public function scopeIsDelivered($query)
    {
        return $query->where('delivery_status', 'delivered');
    }

    public function scopeIsCancelled($query)
    {
        return $query->where('delivery_status', 'cancelled');
    }

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function orderGroup()
    {
        return $this->belongsTo(OrderGroup::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function orderUpdates()
    {
        return $this->hasMany(OrderUpdate::class)->latest();
    }

    public function latestDeliveryStatusUpdate()
    {
        return $this->hasOne(OrderUpdate::class)
            ->where('type', 'default')
            ->whereIn('status', [
                'Order Placed',
                'Confirmed',
                'Processing',
                'Cancelled',
                'Shipped',
                'Delivered',
            ])
            ->latestOfMany();
    }

    public function commissionHistory()
    {
        return $this->hasOne(CommissionHistory::class);
    }

    public function agentCommissionHistory()
    {
        return $this->hasOne(AgentCommissionHistory::class);
    }

    public function returnRefunds()
    {
        return $this->hasMany(ReturnRequest::class);
    }

    public function originalOrder()
    {
        return $this->belongsTo(Order::class, 'original_order_id');
    }

    public function exchangeAsOriginal()
    {
        return $this->hasOne(Exchange::class, 'original_order_id');
    }

    public function exchangeAsReplacement()
    {
        return $this->hasOne(Exchange::class, 'exchange_order_id');
    }

    public function profitLog()
    {
        return $this->hasOne(OrderProfitLog::class);
    }
}
