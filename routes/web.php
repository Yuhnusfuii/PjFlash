<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Auth\GoogleController;

// Livewire pages
use App\Livewire\Decks\DeckIndex;
use App\Livewire\Decks\DeckShow;
use App\Livewire\Decks\DeckForm;
use App\Livewire\Study\StudyPanel;
use App\Livewire\Analytics\DeckAnalytics;
use App\Livewire\Analytics\DecksOverview;
use App\Livewire\Items\FlashcardForm;
use App\Models\Deck;

/*
|--------------------------------------------------------------------------
| WEB ROUTES
|--------------------------------------------------------------------------
| Laravel 11 + Breeze + Sanctum + Livewire 3
|--------------------------------------------------------------------------
*/

// 🌐 Public
Route::view('/', 'home')->name('home');

// OAuth (Google)
Route::get('auth/google/redirect', [GoogleController::class, 'redirect'])
    ->name('oauth.google.redirect');
Route::get('auth/google/callback', [GoogleController::class, 'callback'])
    ->name('oauth.google.callback');

// Logout (Breeze)
Route::post('/logout', function (Request $request) {
    Auth::guard('web')->logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect()->route('login');
})->name('logout');

// 🔒 App pages (auth + verified)
Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard
    Route::view('/dashboard', 'dashboard')->name('dashboard');

    // --- Aliases giữ tương thích view cũ ---
    Route::get('/items', fn () => redirect()->route('decks.index'))
        ->name('items.index');

    Route::get('/settings', fn () => redirect()->route('profile.edit'))
        ->name('settings');

    // legacy: study.panel
    Route::get('/study/{deck:id}', function (Deck $deck) {
        return redirect()->route('decks.study', ['deck' => $deck->id]);
    })->name('study.panel');

    // --- Decks (vẫn bind deck theo id ở các trang deck/ study/ analytics) ---
    Route::get('/decks', DeckIndex::class)->name('decks.index');
    Route::get('/decks/create', DeckForm::class)->name('decks.create');
    Route::get('/decks/{deck:id}', DeckShow::class)->name('decks.show');
    Route::get('/decks/{deck:id}/edit', DeckForm::class)->name('decks.edit');

    // Study
    Route::get('/decks/{deck:id}/study', StudyPanel::class)->name('decks.study');

    // Analytics
    Route::get('/decks/{deck:id}/analytics', DeckAnalytics::class)->name('decks.analytics');
    Route::get('/analytics/decks', DecksOverview::class)->name('analytics.decks');
    Route::get('/analytics', DecksOverview::class)->name('analytics.index');

    // --- Flashcards (Livewire Form) — dùng tham số THÔ để tránh binding ---
    // Đặt /create TRƯỚC {itemId}
    Route::get('/decks/{deckId}/flashcards/create', FlashcardForm::class)
        ->whereNumber('deckId')
        ->name('flashcards.create');

    Route::get('/decks/{deckId}/flashcards/{itemId}/edit', FlashcardForm::class)
        ->whereNumber('deckId')
        ->whereNumber('itemId')
        ->name('flashcards.edit');
});

// Breeze auth routes
require __DIR__ . '/auth.php';
