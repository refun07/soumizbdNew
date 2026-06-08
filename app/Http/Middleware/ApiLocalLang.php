<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ApiLocalLang
{
    public function handle(Request $request, Closure $next)
    {

        if ($request->hasHeader('Accept-Language')) {
            $rawLocale = $request->header('Accept-Language');
            // Get the first language part before any comma (e.g., "en,en;q=0.9" => "en")
            $locale = strtolower(trim(explode(',', $rawLocale)[0]));
        } else {
            $locale = config('app.default_language');
        }

        // Optional: allow only supported locales
        $supportedLocales = ['en', 'bn']; // update this as per your app
        if (!in_array($locale, $supportedLocales)) {
            $locale = config('app.default_language');
        }

        // Set Laravel locale
        app()->setLocale($locale);

        return $next($request);
    }
}
