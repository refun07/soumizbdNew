<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * Get normal (logged-in) users with abandoned carts
     *
     * @param int $abandonedMinutes Minutes of inactivity to consider abandoned
     * @return \Illuminate\Support\Collection
     */
    public static function getAbandonedCartsByUsers($abandonedMinutes = 60)
    {
        $threshold = now()->subMinutes($abandonedMinutes);

        return static::select('user_id', \DB::raw('MAX(updated_at) as last_activity'))
            ->whereNotNull('user_id')
            ->where('updated_at', '<', $threshold)
            ->groupBy('user_id')
            ->having('last_activity', '<', $threshold)
            ->get();
    }

    /**
     * Get guest users with abandoned carts
     *
     * @param int $abandonedMinutes Minutes of inactivity to consider abandoned
     * @return \Illuminate\Support\Collection
     */
    public static function getAbandonedCartsByGuests($abandonedMinutes = 60)
    {
        $threshold = now()->subMinutes($abandonedMinutes);

        return static::select('guest_user_id', \DB::raw('MAX(updated_at) as last_activity'))
            ->whereNotNull('guest_user_id')
            ->whereNull('user_id')
            ->where('updated_at', '<', $threshold)
            ->groupBy('guest_user_id')
            ->having('last_activity', '<', $threshold)
            ->get();
    }

    /**
     * Check if a specific user has an abandoned cart
     *
     * @param int|null $userId Normal user ID
     * @param int|null $guestUserId Guest user ID
     * @param int $abandonedMinutes Minutes of inactivity to consider abandoned
     * @return bool
     */
    public static function isAbandonedCart($userId = null, $guestUserId = null, $abandonedMinutes = 60)
    {
        $threshold = now()->subMinutes($abandonedMinutes);

        $query = static::where('updated_at', '<', $threshold);

        if (!is_null($userId)) {
            $query->where('user_id', $userId);
        } elseif (!is_null($guestUserId)) {
            $query->where('guest_user_id', $guestUserId);
        } else {
            return false;
        }

        return $query->exists();
    }

    public function productVariation()
    {
        return $this->belongsTo(ProductVariation::class);
    }
}
