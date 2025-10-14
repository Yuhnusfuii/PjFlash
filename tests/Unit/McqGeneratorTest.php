<?php

use App\Models\User;
use App\Models\Deck;
use App\Models\Item;
use App\Services\Quiz\McqGenerator;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function seedDeckForMcq(): Deck {
    $u = User::factory()->create();
    $d = Deck::create(['user_id' => $u->id, 'name' => 'MCQ Deck']);
    $pairs = [
        ['Hello', 'Xin chào'],
        ['Bye', 'Tạm biệt'],
        ['Dog', 'Chó'],
        ['Cat', 'Mèo'],
        ['Bird', 'Chim'],
    ];
    foreach ($pairs as [$f,$b]) {
        Item::create(['deck_id'=>$d->id,'front'=>$f,'back'=>$b,'type'=>'flashcard']);
    }
    return $d;
}

test('mcq generator creates questions with 4 shuffled options and valid modes', function () {
    $deck = seedDeckForMcq();
    $gen = app(McqGenerator::class);

    foreach (['mixed','front_to_back','back_to_front'] as $mode) {
        $qs = $gen->make($deck, 5, $mode);
        expect($qs)->toBeArray()->and(count($qs))->toBeGreaterThan(0);
        foreach ($qs as $q) {
            expect($q)->toHaveKeys(['qid','question','answers','correct','direction']);
            expect($q['answers'])->toBeArray()->and(count($q['answers']))->toBe(4);
            expect(in_array($q['correct'], $q['answers'], true))->toBeTrue();

            if ($mode !== 'mixed') {
                expect($q['direction'])->toBe($mode);
            } else {
                expect(in_array($q['direction'], ['front_to_back','back_to_front'], true))->toBeTrue();
            }
        }
    }
});
