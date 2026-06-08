<?php

namespace App\Http\Middleware;

use Closure;
use App;
use Session;

class LanguageMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (Session::has('locale')) {
            $locale = Session::get('locale');
        } elseif (config('app.default_language') != null) {
            $locale = config('app.default_language');
        } else {
            $locale = 'en-US';
        }

        App::setLocale($locale);
        $request->session()->put('locale', $locale);

        return $next($request);
    }
}
