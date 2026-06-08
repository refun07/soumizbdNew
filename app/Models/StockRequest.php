<?php

namespace App\Models;

use App\Traits\CreatedUpdatedDeletedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockRequest extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'guest_user_id',
        'email',
        'phone',
        'product_id',
        'product_variation_id',
        'product_name',
        'product_sku',
        'product_image',
        'product_details',
        'quantity',
        'customer_note',
        'status',
        'admin_note',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'product_details' => 'array',
        'quantity' => 'integer',
        'status' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function product()
    {
        return $this->belongsTo(Product::class)->withTrashed();
    }

    public function productVariation()
    {
        return $this->belongsTo(ProductVariation::class)->withTrashed();
    }

    public function scopePending($query)
    {
        return $query->where('status', 0);
    }

    public function scopeFulfilled($query)
    {
        return $query->where('status', 1);
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 2);
    }

    public function getStatusTextAttribute()
    {
        return match($this->status) {
            0 => 'Pending',
            1 => 'Fulfilled',
            2 => 'Cancelled',
            default => 'Unknown',
        };
    }

    public function getStatusClassAttribute()
    {
        return match($this->status) {
            0 => 'badge-soft-warning',
            1 => 'badge-soft-success',
            2 => 'badge-soft-danger',
            default => '',
        };
    }

    public function getCustomerTypeAttribute()
    {
        return $this->user_id ? 'Registered' : 'Guest';
    }

    public function getCustomerNameAttribute()
    {
        if ($this->user_id && $this->user) {
            return $this->user->name;
        }
        return 'Guest User';
    }
}
