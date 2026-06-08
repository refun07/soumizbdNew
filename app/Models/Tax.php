<?php

namespace App\Models;

use App\Traits\CreatedUpdatedDeletedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tax extends Model
{
    use HasFactory, SoftDeletes, CreatedUpdatedDeletedBy;

    protected $guarded = [];

    public  function scopeIsActive($query)
    {
        return $query->where('is_active', 1);
    }
}
