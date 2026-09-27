<?php

namespace App\Models;

use App\Traits\CreatedUpdatedDeletedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class BrandGroup extends Model
{
    use HasFactory, SoftDeletes, CreatedUpdatedDeletedBy;

    # prefix the group select uses for groups typed in on the fly
    const NEW_GROUP_PREFIX = 'new:';

    protected $guarded = [];

    public function brands()
    {
        return $this->hasMany(Brand::class);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    # unique slug from a name, ignoring the given group id
    public static function makeSlug($name, $ignoreId = null)
    {
        $base = Str::slug($name, '-') ?: Str::random(6);
        $slug = $base;
        $i    = 2;

        while (static::withTrashed()->where('slug', $slug)->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $slug = $base . '-' . $i++;
        }

        return $slug;
    }

    # turn the group select value into a group id; creates the group when typed in on the fly
    public static function resolveId($value)
    {
        $value = trim((string) $value);
        if ($value === '') {
            return null;
        }

        if (Str::startsWith($value, self::NEW_GROUP_PREFIX)) {
            $name = trim(Str::after($value, self::NEW_GROUP_PREFIX));
            if ($name === '') {
                return null;
            }

            $group = static::whereRaw('LOWER(name) = ?', [Str::lower($name)])->first();
            if (!$group) {
                $group = static::create([
                    'name' => $name,
                    'slug' => static::makeSlug($name),
                ]);
            }

            return $group->id;
        }

        return static::whereKey($value)->value('id');
    }
}
