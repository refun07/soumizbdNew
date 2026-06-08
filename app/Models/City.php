<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class City extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public function scopeIsActive($query)
    {
        return $query->where('is_active', 1);
    }

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function zone()
    {
        return $this->belongsTo(Zone::class);
    }
}
