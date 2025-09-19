<?php

use App\Models\{User, Deck, Item};
use App\Services\Contracts\MatchingGeneratorServiceInterface;

function pairsAreWellFormed(array $pairs): void {
    foreach ($pairs as $p) {
        expect($p)->toHaveKeys(['left', 'right']);
        expect($p['left'])->toBeString();
        expect($p['right'])->toBeString();
    }
}

function pairsAreDistinct(array $pairs): void {
    $seen = [];
    foreach ($pairs as $p) {
        $key = $p['left'].'|'.$p['right'];
        expect(isset($seen[$key]))->toBeFalse();
        $seen[$key] = true;
    }
}

it('Matching generator returns valid pairs without duplicates', function () {
    $user = User::factory()->create();
    $deck = Deck::factory()->create(['user_id' => $user->id]);

    // tạo thêm vài cặp để có dữ liệu
    Item::factory()->count(6)->create([
        'deck_id' => $deck->id,
        'type' => 'flashcard',
    ]);

    $target = Item::factory()->create([
        'deck_id' => $deck->id,
        'type' => 'flashcard',
        'front' => 'dog',
        'back'  => 'con chó',
    ]);

    $gen = app(MatchingGeneratorServiceInterface::class);
    $res = $gen->generate($target, $deck, 4);

    expect($res)->toBeArray()->and($res)->toHaveKey('pairs');
    expect($res['pairs'])->toBeArray()->and(count($res['pairs']))->toBeGreaterThanOrEqual(3);

    pairsAreWellFormed($res['pairs']);
    pairsAreDistinct($res['pairs']);
});
