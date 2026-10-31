<?php

namespace App\Services;

class StorefrontTestimonials
{
    public static function rows(array $avatars, array $names, array $designations, array $texts, array $ratings, callable $avatarUrl): array
    {
        $rows = [];
        $count = max(count($avatars), count($names), count($texts));
        for ($index = 0; $index < $count; $index++) {
            $name = trim($names[$index] ?? '');
            $text = trim($texts[$index] ?? '');
            if ($name === '' || $text === '') continue;
            $rating = $ratings[$index] ?? '';
            $rating = is_numeric($rating) && $rating >= 1 && $rating <= 5 ? (string) $rating : '';
            $rows[] = [
                'avatar' => empty($avatars[$index]) ? '' : $avatarUrl($avatars[$index]),
                'name' => $name,
                'designation' => trim($designations[$index] ?? ''),
                'text' => $text,
                'rating' => $rating,
            ];
        }
        return $rows;
    }
}
