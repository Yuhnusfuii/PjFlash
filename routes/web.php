<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\GoogleController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
// Home (public)
Route::view('/', 'home')->name('home');

// ✅ OAuth Google (PUBLIC)
Route::get('/auth/google/redirect', [GoogleController::class, 'redirect'])
    ->name('oauth.google.redirect');
Route::get('/auth/google/callback', [GoogleController::class, 'callback'])
    ->name('oauth.google.callback');

// Dashboard và các trang khác (có auth), sau cần chỉnh thì thêm "verified"
Route::middleware(['auth'])->group(function () { 
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