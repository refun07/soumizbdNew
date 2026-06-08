<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderUpdate extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getActor()
    {
        return $this->creator ?? $this->user;
    }

    public function getActorName()
    {
        return $this->getActor()?->name;
    }

    public function getActorRole()
    {
        $actor = $this->getActor();

        if (!$actor) {
            return null;
        }

        return match ($actor->user_type) {
            'admin' => 'Admin',
            'seller' => 'Seller',
            'staff' => 'Staff',
            'customer' => 'Customer',
            default => ucfirst($actor->user_type),
        };
    }

    public function getCreatorRole()
    {
        return $this->getActorRole();
    }
}
