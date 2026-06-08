<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeatureFlag extends Model
{
    use HasFactory;

    protected $guarded = [];

    public static function isEnabled(string $key): bool
    {
        $flag = static::where('key', $key)->first();
        return $flag && $flag->is_enabled;
    }
}
