<?php

namespace App\Models;

use App\Traits\CreatedUpdatedDeletedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

# a collection shared by any number of brands; a product can sit in one collection of its brand
class ProductCollection extends Model
{
    use HasFactory, SoftDeletes, CreatedUpdatedDeletedBy;

    # prefix the collection select uses for collections typed in on the fly
    const NEW_COLLECTION_PREFIX = 'new:';

    protected $table = 'collections';

    protected $guarded = [];

    public function brands()
    {
        return $this->belongsToMany(Brand::class, 'brand_collection', 'collection_id', 'brand_id');
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'collection_id');
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    # unique slug from a name, ignoring the given collection id
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

    # turn the collection select values into collection ids; creates collections typed in on the fly
    public static function resolveIds($values)
    {
        $ids = [];

        foreach ((array) $values as $value) {
            $value = trim((string) $value);
            if ($value === '') {
                continue;
            }

            if (Str::startsWith($value, self::NEW_COLLECTION_PREFIX)) {
                $name = trim(Str::after($value, self::NEW_COLLECTION_PREFIX));
                if ($name === '') {
                    continue;
                }

                $collection = static::whereRaw('LOWER(name) = ?', [Str::lower($name)])->first();
                if (!$collection) {
                    $collection = static::create([
                        'name' => $name,
                        'slug' => static::makeSlug($name),
                    ]);
                }

                $ids[] = $collection->id;
                continue;
            }

            if ($id = static::whereKey($value)->value('id')) {
                $ids[] = $id;
            }
        }

        return array_values(array_unique($ids));
    }
}
