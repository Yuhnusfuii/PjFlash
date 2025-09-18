<?php

use App\Enums\ReviewRating;
use App\Models\{User, Deck, Item};
use App\Services\SrsService;

it('updates EF & interval with SM-2', function () {
    $user = User::factory()->create();
    $deck = Deck::factory()->create(['user_id'=>$user->id]);
    $item = Item::create(['deck_id'=>$deck->id,'type'=>'flashcard','front'=>'A','back'=>'a']);

    $srs  = app(SrsService::class);
    $st   = $srs->review($user, $item, ReviewRating::GOOD);

    expect($st->interval)->toBeGreaterThan(0)
        ->and($st->ease)->toBeGreaterThan(1.29)
        ->and($st->due_at)->not->toBeNull();
});
