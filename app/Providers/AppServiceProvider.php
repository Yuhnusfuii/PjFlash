<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
{
    if (app()->environment('production')) {
            URL::forceScheme('https');
        }
    RateLimiter::for('review', function (Request $request) {
        return [
            Limit::perMinute(120)->by(optional($request->user())->id ?: $request->ip()),
        ];
    });

    RateLimiter::for('import', function (Request $request) {
        return [
            Limit::perMinute(20)->by(optional($request->user())->id ?: $request->ip()),
        ];
    });
}
}
