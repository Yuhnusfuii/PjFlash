<?php

use App\Models\{User, Deck, Item};
use App\Services\Contracts\McqGeneratorServiceInterface;

function assertUnique(array $arr): void {
    expect(count($arr))->toBe(count(array_unique($arr)));
}

it('MCQ generator returns valid structure with distinct options & valid answer index', function () {
    $user = User::factory()->create();
    $deck = Deck::factory()->create(['user_id' => $user->id]);

    // tạo vài item để generator có chất liệu distractor
    Item::factory()->count(8)->create([
        'deck_id' => $deck->id,
        'type' => 'flashcard',
    ]);

    $target = Item::factory()->create([
        'deck_id' => $deck->id,
        'type' => 'flashcard',
        'front' => 'Capital of France?',
        'back'  => 'Paris',
    ]);

    $gen = app(McqGeneratorServiceInterface::class);
    $mcq = $gen->generate($target, $deck, 4);

    expect($mcq)->toBeArray()
        ->and($mcq)->toHaveKeys(['question', 'options', 'answer'])
        ->and($mcq['question'])->toBeString()
        ->and($mcq['options'])->toBeArray()
        ->and(count($mcq['options']))->toBeGreaterThanOrEqual(3);

    // options unique & answer index hợp lệ
    assertUnique($mcq['options']);
    expect($mcq['answer'])->toBeInt()
        ->and($mcq['answer'])->toBeGreaterThanOrEqual(0)
        ->and($mcq['answer'])->toBeLessThan(count($mcq['options']));
});
