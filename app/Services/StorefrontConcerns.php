<?php

namespace App\Services;

class StorefrontConcerns
{
    public static function all(): array
    {
        return json_decode(file_get_contents(resource_path('storefront/concerns.json')), true, 512, JSON_THROW_ON_ERROR);
    }

    public static function find(string $slug): ?array
    {
        foreach (self::all() as $concern) {
            if ($concern['slug'] === $slug) return $concern;
        }
        return null;
    }
}
