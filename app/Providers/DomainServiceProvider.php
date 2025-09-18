<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class DomainServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(\App\Services\SrsService::class);
        $this->app->singleton(\App\Services\McqGeneratorService::class);
        $this->app->singleton(\App\Services\MatchingGeneratorService::class);
    }
}
