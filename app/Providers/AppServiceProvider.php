<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Http\Resources\Json\JsonResource;
use URL;
use Illuminate\Support\Facades\Schema;
use FacebookConversionService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(FacebookConversionService::class, function ($app) {
            return new FacebookConversionService();
        });
        $this->app->singleton('gtag', function ($app) {
            return new \App\Services\GtagService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        JsonResource::withoutWrapping();

        Schema::defaultStringLength(191);

        if (config('app.force_https')) {
            URL::forceScheme('https');
        }
    }

}
