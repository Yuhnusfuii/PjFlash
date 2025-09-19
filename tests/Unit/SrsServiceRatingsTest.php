<?php

use App\Enums\ReviewRating;
use App\Models\{User, Deck, Item};
use App\Services\Contracts\SrsServiceInterface;
use Illuminate\Support\Carbon;

it('SM-2: AGAIN drops EF and sets short interval', function () {
    Carbon::setTestNow('2025-01-01 00:00:00');

    $user = User::factory()->create();
    $deck = Deck::factory()->create(['user_id' => $user->id]);
    $item = Item::factory()->create(['deck_id' => $deck->id, 'type' => 'flashcard']);

    $srs = app(SrsServiceInterface::class);
    $state = $srs->review($user, $item, ReviewRating::AGAIN, 0);

    expect($state->interval)->toBeGreaterThanOrEqual(0)
        ->and($state->ease)->toBeLessThanOrEqual(2.5) // tùy logic, thường EF giảm mạnh
        ->and($state->due_at)->toBeInstanceOf(Carbon::class)
        ->and($state->due_at->greaterThanOrEqualTo(Carbon::now()))->toBeTrue();
});

it('SM-2: HARD reduces EF slightly and increases interval slowly', function () {
    Carbon::setTestNow('2025-01-01 00:00:00');

    $user = User::factory()->create();
    $deck = Deck::factory()->create(['user_id' => $user->id]);
    $item = Item::factory()->create(['deck_id' => $deck->id, 'type' => 'flashcard']);

    $srs = app(SrsServiceInterface::class);

    // lần 1
    $st1 = $srs->review($user, $item, ReviewRating::HARD, 0);
    // lần 2 để so sánh progression
    Carbon::setTestNow('2025-01-02 00:00:00');
    $st2 = $srs->review($user, $item, ReviewRating::HARD, 0);

    expect($st2->interval)->toBeGreaterThanOrEqual($st1->interval)
        ->and($st2->ease)->toBeLessThanOrEqual($st1->ease) // HARD làm ease giảm
        ->and($st2->due_at->greaterThan($st1->due_at))->toBeTrue();
});

it('SM-2: GOOD keeps EF around baseline and grows interval reasonably', function () {
    Carbon::setTestNow('2025-01-01 00:00:00');

    $user = User::factory()->create();
    $deck = Deck::factory()->create(['user_id' => $user->id]);
    $item = Item::factory()->create(['deck_id' => $deck->id, 'type' => 'flashcard']);

    $srs = app(SrsServiceInterface::class);

    $st1 = $srs->review($user, $item, ReviewRating::GOOD, 0);
    Carbon::setTestNow('2025-01-03 00:00:00');
    $st2 = $srs->review($user, $item, ReviewRating::GOOD, 0);

    expect($st1->interval)->toBeGreaterThan(0)
        ->and($st2->interval)->toBeGreaterThan($st1->interval)
        ->and($st2->ease)->toBeGreaterThanOrEqual(1.3); // EF không tụt quá thấp
});

it('SM-2: EASY increases EF and interval faster', function () {
    Carbon::setTestNow('2025-01-01 00:00:00');

    $user = User::factory()->create();
    $deck = Deck::factory()->create(['user_id' => $user->id]);
    $item = Item::factory()->create(['deck_id' => $deck->id, 'type' => 'flashcard']);

    $srs = app(SrsServiceInterface::class);

    $st1 = $srs->review($user, $item, ReviewRating::GOOD, 0);
    Carbon::setTestNow('2025-01-04 00:00:00');
    $st2 = $srs->review($user, $item, ReviewRating::EASY, 0);

    expect($st2->interval)->toBeGreaterThan($st1->interval)
        ->and($st2->ease)->toBeGreaterThan($st1->ease);
});
