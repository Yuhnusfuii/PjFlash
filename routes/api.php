<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Http\Request;
use Illuminate\Cache\RateLimiting\Limit;

use App\Http\Controllers\Api\AuthController             as ApiAuthController;
use App\Http\Controllers\Api\DeckController             as ApiDeckController;
use App\Http\Controllers\Api\StudyController            as ApiStudyController;
use App\Http\Controllers\Api\ItemController             as ApiItemController;
use App\Http\Controllers\Api\FlashcardController        as ApiFlashcardController;
use App\Http\Controllers\Api\AnalyticsController        as ApiAnalyticsController;
use App\Http\Controllers\Api\GeneratorController        as ApiGeneratorController;

/*
|--------------------------------------------------------------------------
| API ROUTES (prefix /api)
|--------------------------------------------------------------------------
| Tất cả endpoint dùng cho Livewire/Alpine fetch để trong file này.
| Tránh để API ở web.php để không bị trùng tên/đụng middleware.
|--------------------------------------------------------------------------
*/

/*
|--------------------------------------------------------------------------
| Rate Limiters
|--------------------------------------------------------------------------
*/
RateLimiter::for('api', function (Request $request) {
    return Limit::perMinute(120)
        ->by(optional($request->user())->id ?: $request->ip())
        ->response(function () {
            return response()->json(['message' => 'Too many requests. Please slow down.'], 429);
        });
});

RateLimiter::for('review', function (Request $request) {
    $userKey = optional($request->user())->id ?: $request->ip();
    $itemId  = (string)($request->route('item')?->id ?? $request->route('item'));

    return [
        Limit::perMinute(300)->by($userKey),
        Limit::perMinute(60)->by($userKey . '|item:' . $itemId),
    ];
});

RateLimiter::for('mcq_generate', function (Request $request) {
    $key = optional($request->user())->id ?: $request->ip();
    return [
        Limit::perMinute(10)->by($key),
        Limit::perDay(200)->by('day|' . $key),
    ];
});

RateLimiter::for('matching_generate', function (Request $request) {
    $key = optional($request->user())->id ?: $request->ip();
    return [
        Limit::perMinute(10)->by($key),
        Limit::perDay(200)->by('day|' . $key),
    ];
});

RateLimiter::for('import', function (Request $request) {
    $key = optional($request->user())->id ?: $request->ip();
    return [
        Limit::perMinute(6)->by($key),
        Limit::perDay(50)->by('day|' . $key),
    ];
});

/*
|--------------------------------------------------------------------------
| (tuỳ chọn) Auth API để lấy Sanctum token qua Postman
|--------------------------------------------------------------------------
*/
Route::post('auth/login', [ApiAuthController::class, 'login'])->middleware('throttle:api');
Route::middleware('auth:sanctum')->group(function () {
    Route::post('auth/logout', [ApiAuthController::class, 'logout'])->middleware('throttle:api');
    Route::get('auth/me', [ApiAuthController::class, 'me'])->middleware('throttle:api');
});

/*
|--------------------------------------------------------------------------
| Protected API Routes (Sanctum)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {

    // ===== A) Decks =====
    Route::get('decks', [ApiDeckController::class, 'index'])
        ->middleware('throttle:api')
        ->name('api.decks.index');

    // NEW: tạo/sửa/xoá deck
    Route::post('decks', [ApiDeckController::class, 'store'])
        ->middleware('throttle:api')
        ->name('api.decks.store');

    Route::get('decks/{deck}', [ApiDeckController::class, 'show'])
        ->middleware('throttle:api')
        ->name('api.decks.show');

    Route::put('decks/{deck}', [ApiDeckController::class, 'update'])
        ->middleware('throttle:api')
        ->name('api.decks.update');

    Route::delete('decks/{deck}', [ApiDeckController::class, 'destroy'])
        ->middleware('throttle:api')
        ->name('api.decks.destroy');

    // Giữ import như cũ (rate-limit 'import')
    Route::post('decks/{deck}/import', [ApiDeckController::class, 'import'])
        ->middleware('throttle:import')
        ->name('api.decks.import');

    // ===== B) Items (CRUD chuẩn – nếu bạn muốn dùng items thô) =====
    Route::get('decks/{deck}/items', [ApiItemController::class, 'index'])->middleware('throttle:api');
    Route::post('decks/{deck}/items', [ApiItemController::class, 'store'])->middleware('throttle:api');
    Route::get('items/{item}', [ApiItemController::class, 'show'])->middleware('throttle:api');
    Route::put('items/{item}', [ApiItemController::class, 'update'])->middleware('throttle:api');
    Route::delete('items/{item}', [ApiItemController::class, 'destroy'])->middleware('throttle:api');

    // ===== C) Flashcards (CRUD theo type='flashcard') =====
    Route::get('decks/{deck}/flashcards', [ApiFlashcardController::class, 'index'])->middleware('throttle:api');
    Route::post('decks/{deck}/flashcards', [ApiFlashcardController::class, 'store'])->middleware('throttle:api');
    Route::get('flashcards/{flashcard}', [ApiFlashcardController::class, 'show'])->middleware('throttle:api');
    Route::put('flashcards/{flashcard}', [ApiFlashcardController::class, 'update'])->middleware('throttle:api');
    Route::delete('flashcards/{flashcard}', [ApiFlashcardController::class, 'destroy'])->middleware('throttle:api');

    // ===== D) Study =====
    Route::get('study/queue', [ApiStudyController::class, 'queue'])
        ->middleware('throttle:review')
        ->name('study.queue');

    Route::post('study/{item}/review', [ApiStudyController::class, 'review'])
        ->middleware('throttle:review')
        ->name('api.study.review');

    // Legacy generator qua StudyController (giữ nguyên cho tương thích)
    Route::post('items/{item}/mcq/generate', [ApiStudyController::class, 'generateMcq'])
        ->middleware('throttle:mcq_generate')
        ->name('api.items.mcq.generate');

    Route::post('items/{item}/matching/generate', [ApiStudyController::class, 'generateMatching'])
        ->middleware('throttle:matching_generate')
        ->name('api.items.matching.generate');

    // ===== E) (tuỳ chọn) Generators API tách riêng =====
    Route::post('generator/mcq', [ApiGeneratorController::class, 'mcq'])->middleware('throttle:mcq_generate');
    Route::post('generator/matching', [ApiGeneratorController::class, 'matching'])->middleware('throttle:matching_generate');

    // ===== F) (tuỳ chọn) Analytics API =====
    Route::get('analytics/decks', [ApiAnalyticsController::class, 'overview'])->middleware('throttle:api');
    Route::get('analytics/decks/{deck}', [ApiAnalyticsController::class, 'deckAnalytics'])->middleware('throttle:api');
});
