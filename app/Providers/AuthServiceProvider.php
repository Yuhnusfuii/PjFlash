<?php

namespace App\Providers;

use App\Models\Deck;
use App\Models\Item;
use App\Policies\DeckPolicy;
use App\Policies\ItemPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Deck::class => DeckPolicy::class,
        Item::class => ItemPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // Mapping trong $policies là đủ
    }
}
