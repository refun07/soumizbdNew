<?php

namespace App\Models;

use Spatie\Permission\Models\Role as ModelsRole;

class Role extends ModelsRole
{
    protected $guarded = [];

    public function scopeIsActive($query)
    {
        return $query->where('is_active', 1);
    }
}
