<?php

namespace App\Traits;

use App\Models\User;

trait CreatedUpdatedDeletedBy
{
    public static function bootCreatedUpdatedDeletedBy()
    {
        static::creating(function ($model) {
            $model->created_by = user() ? user()->id : null;
            $model->updated_by = user() ? user()->id : null;
        });

        static::updating(function ($model) {
            $model->updated_by = user() ? user()->id : null;
        });

        static::deleting(function ($model) {
            $model->deleted_by = user() ? user()->id : null;
        });
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function deletedBy()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }
}
