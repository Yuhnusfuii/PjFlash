<?php

use App\Models\User;
use App\Models\Deck;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

test('owner can delete a flashcard', function () {
    $user = User::factory()->create();
    $deck = Deck::create(['user_id' => $user->id, 'name' => 'D1']);
    $item = Item::create(['deck_id' => $deck->id, 'front' => 'F', 'back' => 'B', 'type' => 'flashcard']);

    Sanctum::actingAs($user);

    // API của bạn trả 200 (OK), không phải 204.
    $this->deleteJson("/api/flashcards/{$item->id}")
        ->assertStatus(200);

    $this->assertDatabaseMissing('items', ['id' => $item->id]);
});

test('non owner cannot delete others flashcard', function () {
    [$a, $b] = User::factory()->count(2)->create();
    $deck = Deck::create(['user_id' => $a->id, 'name' => 'A']);
    $item = Item::create(['deck_id' => $deck->id, 'front' => 'F', 'back' => 'B', 'type' => 'flashcard']);

    Sanctum::actingAs($b);

    $this->deleteJson("/api/flashcards/{$item->id}")
        ->assertStatus(403);
});
