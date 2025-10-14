<?php

use App\Models\User;
use App\Models\Deck;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

test('owner can create flashcard via API', function () {
    $user = User::factory()->create();
    $deck = Deck::create(['user_id' => $user->id, 'name' => 'D1']);
    Sanctum::actingAs($user);

    $resp = $this->postJson("/api/decks/{$deck->id}/flashcards", [
        'front' => 'Hello',
        'back'  => 'Xin chào',
        'type'  => 'flashcard',
    ]);

    $resp->assertCreated()->assertJsonPath('deck_id', $deck->id);
});

test('cannot create flashcard without required fields', function () {
    $user = User::factory()->create();
    $deck = Deck::create(['user_id' => $user->id, 'name' => 'D1']);
    Sanctum::actingAs($user);

    $this->postJson("/api/decks/{$deck->id}/flashcards", [
        'front' => 'Only front',
    ])->assertStatus(422)->assertJsonValidationErrors(['back']);
});

test('non owner is forbidden to add flashcard to others deck', function () {
    [$a, $b] = User::factory()->count(2)->create();
    $deck = Deck::create(['user_id' => $a->id, 'name' => 'A deck']);
    Sanctum::actingAs($b);

    $this->postJson("/api/decks/{$deck->id}/flashcards", [
        'front' => 'X', 'back' => 'Y', 'type' => 'flashcard',
    ])->assertStatus(403);
});
