<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

test('can create deck via API with valid data', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $resp = $this->postJson('/api/decks', [
        'name' => 'My Deck',
        'description' => 'from test',
    ]);

    $resp->assertCreated()
        ->assertJsonPath('name', 'My Deck')
        ->assertJsonPath('user_id', $user->id);
});

test('cannot create deck via API without name', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $this->postJson('/api/decks', [])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['name']);
});

test('guest is unauthorized to create deck via API', function () {
    $this->postJson('/api/decks', ['name' => 'X'])->assertStatus(401);
});
