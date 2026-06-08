<?php

namespace App\Models;

use App\Traits\CreatedUpdatedDeletedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BlockedIp extends Model
{
    use HasFactory, SoftDeletes, CreatedUpdatedDeletedBy;

    protected $guarded = ['id'];

    protected $casts = [
        'is_active' => 'boolean',
        'blocked_until' => 'datetime',
    ];

    /**
     * Scope to get only active blocked IPs
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }

    /**
     * Scope to get currently blocked IPs (active and not expired)
     */
    public function scopeCurrentlyBlocked($query)
    {
        return $query->where('is_active', 1)
            ->where(function ($q) {
                $q->whereNull('blocked_until')
                  ->orWhere('blocked_until', '>', now('UTC'));
            });
    }

    /**
     * Check if an IP is currently blocked
     */
    public static function isBlocked(string $ipAddress): bool
    {
        return self::currentlyBlocked()
            ->where('ip_address', $ipAddress)
            ->exists();
    }

    /**
     * Get the admin who created this block
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the admin who updated this block
     */
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}