<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\ProfileController;

use App\Livewire\Decks\DeckIndex;
use App\Livewire\Decks\DeckShow;
use App\Livewire\Decks\DeckForm;
use App\Livewire\Study\StudyPanel;
use App\Livewire\Analytics\DeckAnalytics;
use App\Livewire\Analytics\DecksOverview;
use App\Livewire\Items\FlashcardForm;
use App\Livewire\Study\McqHome;
use App\Livewire\Study\McqPanel;
use App\Livewire\Study\McqHistory;
use App\Livewire\Study\McqAllPanel;
use App\Models\Deck;
use App\Livewire\Explore\ExploreDecks;
use App\Livewire\Explore\PublicDeckShow;

/*
|--------------------------------------------------------------------------
| WEB ROUTES
|--------------------------------------------------------------------------
*/

Route::view('/', 'home')->name('home');

// OAuth
Route::get('auth/google/redirect', [GoogleController::class, 'redirect'])->name('oauth.google.redirect');
Route::get('auth/google/callback', [GoogleController::class, 'callback'])->name('oauth.google.callback');

// Logout (Breeze)
Route::post('/logout', function (Request $request) {
    Auth::guard('web')->logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect()->route('login');
})->name('logout');
Route::get('/explore', ExploreDecks::class)->name('explore.index');
Route::get('/p/{slug}', PublicDeckShow::class)->name('explore.show');
// 🔒 App
Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    // Aliases giữ tương thích
    Route::get('/items', fn () => redirect()->route('decks.index'))->name('items.index');
    Route::get('/study/{deck:id}', function (Deck $deck) {
        return redirect()->route('decks.study', ['deck' => $deck->id]);
    })->name('study.panel');

    // Profile
    Route::get('/profile',  [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar');
    Route::get('/settings', fn () => redirect()->route('profile.edit'))->name('settings');

    // Decks
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

    // Flashcards (Form)
    Route::get('/decks/{deckId}/flashcards/create', FlashcardForm::class)
        ->whereNumber('deckId')->name('flashcards.create');
    Route::get('/decks/{deckId}/flashcards/{itemId}/edit', FlashcardForm::class)
        ->whereNumber('deckId')->whereNumber('itemId')->name('flashcards.edit');

    // MCQ (home + theo deck + global + history)
    Route::get('/mcq', McqHome::class)->name('mcq.home');
    Route::get('/decks/{deck}/mcq', McqPanel::class)->name('decks.mcq');
    Route::get('/mcq/all', McqAllPanel::class)->name('mcq.all');
    Route::get('/mcq/history', McqHistory::class)->name('mcq.history');
});

// Debug testcase page
Route::get('/debug/tests', function () {
    $tests = [
        'AuthController' => [
            'BB1' => 'Đăng nhập thành công – redirect dashboard',
            'BB2' => 'Sai mật khẩu – bị từ chối',
        ],
        'DeckController' => [
            'BB3' => 'Tạo deck hợp lệ (201)',
            'BB4' => 'Thiếu name (422)',
        ],
        'FlashcardController' => [
            'BB5' => 'Cấm tạo flashcard vào deck người khác (403)',
        ],
        'McqGeneratorService' => [
            'WB1' => 'Sinh 4 lựa chọn + 1 đúng; mode mixed/front/back',
        ],
        'DeckPolicy' => [
            'WB2' => 'Quyền owner vs non-owner',
        ],
        'ScheduleDeckQuizzesCommand' => [
            'WB3' => 'quiz:schedule-weekly idempotent',
        ],
        'ProfilePasswordRequest' => [
            'WB4' => 'current_password, confirmed, min',
        ],
        'ProfileAvatarRequest' => [
            'WB5' => 'mime + max size',
        ],
    ];

    return view('debug.testcases', compact('tests'));
});

Route::get('/debug/coverage', function () {
    abort_unless(app()->isLocal(), 403);
    $path = public_path('coverage/index.html');
    if (! file_exists($path)) abort(404, 'Chưa có coverage. Chạy: php artisan test --coverage-html public/coverage');
    return redirect('/coverage/index.html');
});

// Breeze auth routes
require __DIR__ . '/auth.php';
