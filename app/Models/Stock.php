<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Stock extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'expiry_date' => 'timestamp',
        'purchase_cost' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the formatted expiry date
     */
    public function getFormattedExpiryDateAttribute()
    {
        return $this->expiry_date ? $this->expiry_date->format('Y-m-d') : null;
    }

    /**
     * Check if the stock is expired
     */
    public function isExpired()
    {
        return $this->expiry_date && $this->expiry_date->isPast();
    }

    /**
     * Check if the stock is expiring soon (within 30 days)
     */
    public function isExpiringSoon($days = 30)
    {
        return $this->expiry_date && $this->expiry_date->diffInDays(now()) <= $days && !$this->isExpired();
    }

    /**
     * Get the days remaining until expiry
     */
    public function getDaysUntilExpiry()
    {
        if (!$this->expiry_date) return null;
        return $this->expiry_date->diffInDays(now(), false);
    }

    /**
     * Get the expiry status for display
     */
    public function getExpiryStatusAttribute()
    {
        if (!$this->expiry_date) return 'no-expiry';

        if ($this->isExpired()) return 'expired';
        if ($this->isExpiringSoon()) return 'expiring-soon';
        return 'fresh';
    }

    /**
     * Get the supplier that owns the stock
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Get the warehouse that owns the stock
     */
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    /**
     * Get the product variation that owns the stock
     */
    public function productVariation()
    {
        return $this->belongsTo(ProductVariation::class);
    }

    /**
     * Get the purchase order item that owns the stock
     */
    public function purchaseOrderItem()
    {
        return $this->belongsTo(PurchaseOrderProductVariation::class, 'purchase_order_item_id');
    }

    /**
     * Get the creator of the stock
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
