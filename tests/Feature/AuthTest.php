<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('login with valid credentials redirects to dashboard', function () {
    $user = User::factory()->create([
        'password' => bcrypt('secret123'),
    ]);

    $resp = $this->post('/login', [
        'email' => $user->email,
        'password' => 'secret123',
    ]);

    $resp->assertStatus(302)->assertRedirect('/dashboard');

    // ✅ dùng helper assertion của Laravel
    $this->assertAuthenticated();   // tương đương Auth::check() === true
});

test('login fails with wrong password', function () {
    $user = User::factory()->create([
        'password' => bcrypt('secret123'),
    ]);

    $resp = $this->from('/login')->post('/login', [
        'email' => $user->email,
        'password' => 'oops',
    ]);

    $resp->assertStatus(302)->assertRedirect('/login');

    // ✅ chưa đăng nhập
    $this->assertGuest();
});

test('guest cannot access dashboard', function () {
    $this->get('/dashboard')
        ->assertStatus(302)
        ->assertRedirect('/login');

    $this->assertGuest();
});
