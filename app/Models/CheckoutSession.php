<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CheckoutSession extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'cart_data' => 'array',
        'is_converted' => 'boolean',
        'cart_total' => 'decimal:2',
        'shipping_charge' => 'decimal:2',
        'converted_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Get the country associated with this checkout session.
     */
    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * Get the state associated with this checkout session.
     */
    public function state()
    {
        return $this->belongsTo(State::class);
    }

    /**
     * Get the city associated with this checkout session.
     */
    public function city()
    {
        return $this->belongsTo(City::class);
    }

    /**
     * Scope: Get abandoned sessions (not converted, updated > X minutes ago)
     */
    public function scopeAbandoned($query, $minutes = null)
    {
        $minutes = $minutes ?? (int) getSetting('abandonedCartThreshold', 60);
        $threshold = now()->subMinutes($minutes);

        return $query->where('is_converted', false)
            ->where('updated_at', '<', $threshold);
    }

    /**
     * Scope: Get active sessions (not converted, recent activity)
     */
    public function scopeActive($query, $minutes = null)
    {
        $minutes = $minutes ?? (int) getSetting('abandonedCartThreshold', 60);
        $threshold = now()->subMinutes($minutes);

        return $query->where('is_converted', false)
            ->where('updated_at', '>=', $threshold);
    }

    /**
     * Scope: Get sessions by user or guest
     */
    public function scopeForUser($query, $userId = null, $guestUserId = null)
    {
        if ($userId) {
            return $query->where('user_id', $userId);
        }

        if ($guestUserId) {
            return $query->where('guest_user_id', $guestUserId);
        }

        return $query;
    }

    /**
     * Find or create a checkout session for a user/guest
     */
    public static function findOrCreateForUser($userId = null, $guestUserId = null)
    {
        $query = self::where('is_converted', false);

        if ($userId) {
            $query->where('user_id', $userId);
        } elseif ($guestUserId) {
            $query->where('guest_user_id', $guestUserId);
        } else {
            return null;
        }

        $session = $query->first();

        if (! $session) {
            $session = new self;
            $session->user_id = $userId;
            $session->guest_user_id = $guestUserId;
            $session->save();
        }

        return $session;
    }

    /**
     * Mark this session as converted to an order
     */
    public function markAsConverted($orderCode)
    {
        $this->is_converted = true;
        $this->converted_at = now();
        $this->order_code = $orderCode;
        $this->save();
    }

    /**
     * Get the full formatted address
     */
    public function getFormattedAddressAttribute()
    {
        $parts = [];

        if ($this->address_line) {
            $parts[] = $this->address_line;
        }

        if ($this->city) {
            $parts[] = $this->city->name;
        }

        if ($this->state) {
            $parts[] = $this->state->name;
        }

        if ($this->country) {
            $parts[] = $this->country->name;
        }

        return implode(', ', $parts);
    }
}
