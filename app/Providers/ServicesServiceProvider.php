<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ServicesServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            \App\Services\Contracts\SrsServiceInterface::class,
            \App\Services\SrsService::class
        );

        $this->app->bind(
            \App\Services\Contracts\McqGeneratorServiceInterface::class,
            \App\Services\McqGeneratorService::class
        );

        $this->app->bind(
            \App\Services\Contracts\MatchingGeneratorServiceInterface::class,
            \App\Services\MatchingGeneratorService::class
        );
    }

    public function boot(): void
    {
        //
    }
}
