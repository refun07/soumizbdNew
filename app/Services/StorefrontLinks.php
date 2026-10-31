<?php

namespace App\Services;

class StorefrontLinks
{
    public static function normalize($value)
    {
        if (is_array($value)) {
            return array_map([self::class, 'normalize'], $value);
        }
        if (!is_string($value)) return $value;

        // Also support saved absolute links pointing to this storefront.
        $root = preg_quote(rtrim(config('app.url'), '/'), '~');
        return preg_replace('~^(' . $root . ')?/products(?=/|[?#]|$)~', '$1/product', $value);
    }

    public static function html(?string $html): string
    {
        return preg_replace_callback('~(href\\s*=\\s*["\x27])([^"\x27]*)(["\x27])~i',
            fn ($match) => $match[1] . self::normalize($match[2]) . $match[3], $html ?? '');
    }
}
