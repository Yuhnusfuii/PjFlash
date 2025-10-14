<?php

use App\Models\User;
use App\Models\Deck;
use App\Policies\DeckPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('deck policy allows owner and denies others', function () {
    [$a, $b] = User::factory()->count(2)->create();
    $deck = Deck::create(['user_id' => $a->id, 'name' => 'Owned']);

    $policy = app(DeckPolicy::class);

    expect($policy->view($a, $deck))->toBeTrue()
        ->and($policy->update($a, $deck))->toBeTrue()
        ->and($policy->delete($a, $deck))->toBeTrue();

    expect($policy->view($b, $deck))->toBeFalse()
        ->and($policy->update($b, $deck))->toBeFalse()
        ->and($policy->delete($b, $deck))->toBeFalse();
});
