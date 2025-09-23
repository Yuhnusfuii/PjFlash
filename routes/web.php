<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\GoogleController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\{
DeckController, ItemController, StudyController, GeneratorController
};
use App\Livewire\Decks\DeckIndex;
use App\Livewire\Decks\DeckShow;
use App\Livewire\Study\StudyPanel;
use App\Livewire\Analytics\DeckAnalytics;
use App\Livewire\Analytics\DecksOverview;


// Home (public)
Route::view('/', 'home')->name('home');

// ✅ OAuth Google (PUBLIC)
Route::get('/auth/google/redirect', [GoogleController::class, 'redirect'])
    ->name('oauth.google.redirect');
Route::get('/auth/google/callback', [GoogleController::class, 'callback'])
    ->name('oauth.google.callback');

// Dashboard và các trang khác (có auth), sau cần chỉnh thì thêm "verified"
Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('/dashboard', 'dashboard')->name('dashboard');
    Route::view('/decks', 'stubs.decks')->name('decks.index');
    Route::view('/items', 'stubs.items')->name('items.index');
    Route::view('/analytics', 'stubs.analytics')->name('analytics.index');
    Route::view('/study', 'stubs.study')->name('study.queue');
    Route::view('/settings', 'stubs.settings')->name('settings');
});

require __DIR__.'/auth.php';

Route::post('/logout', function (Request $request) {
    Auth::guard('web')->logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect()->route('login'); // hoặc '/'
})->name('logout');

Route::middleware(['auth:sanctum'])->group(function () {

    // Decks
    Route::apiResource('decks', DeckController::class);

    // Items
    Route::apiResource('items', ItemController::class);
    Route::post('items/import', [ItemController::class, 'import'])
        ->middleware('throttle:import');

    // Study
    Route::get('study/queue', [StudyController::class, 'queue'])
        ->middleware('throttle:review');
    Route::post('study/{item}/review', [StudyController::class, 'review'])
        ->middleware('throttle:review');

    // Generators
    Route::get('generate/mcq/{item}', [GeneratorController::class, 'mcq'])
        ->middleware('throttle:review');
    Route::get('generate/matching/{item}', [GeneratorController::class, 'matching'])
        ->middleware('throttle:review');
    });
    Route::middleware(['auth', 'verified'])->group(function () {
        Route::get('/decks', DeckIndex::class)->name('decks.index');
        Route::get('/decks/{deck}', DeckShow::class)->name('decks.show');
    });
    Route::middleware(['auth','verified'])->group(function () {
    Route::get('/decks/{deck}/study', StudyPanel::class)->name('study.panel');
    });
    Route::middleware(['auth','verified'])->group(function () {
    Route::get('/decks/{deck}/analytics', DeckAnalytics::class)->name('decks.analytics');
    });

    Route::middleware(['auth','verified'])->group(function () {
        Route::get('/analytics/decks', DecksOverview::class)->name('analytics.decks');
    });
