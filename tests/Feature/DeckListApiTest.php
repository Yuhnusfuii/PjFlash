<?php

use App\Models\User;
use App\Models\Deck;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

test('list api returns only current user decks', function () {
    [$a, $b] = User::factory()->count(2)->create();

    $d1 = Deck::create(['user_id' => $a->id, 'name' => 'A1']);
    $d2 = Deck::create(['user_id' => $a->id, 'name' => 'A2']);
    $d3 = Deck::create(['user_id' => $b->id, 'name' => 'B1']);

    Sanctum::actingAs($a);

    $resp = $this->getJson('/api/decks')->assertOk();
    $data = $resp->json();

    // Tuỳ response dạng pagination hay collection, kiểm tra linh hoạt
    $payload = is_array($data) && array_is_list($data) ? $data : ($data['data'] ?? []);
    $names = collect($payload)->pluck('name');

    expect($names)->toContain('A1', 'A2')->not()->toContain('B1');
});
