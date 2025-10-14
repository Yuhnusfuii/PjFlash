<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

uses(RefreshDatabase::class);

/**
 * Bộ rules tương đương ProfilePasswordRequest:
 * current_password|required|current_password
 * password|required|string|min:8|confirmed
 */
function passwordRules(): array {
    return [
        'current_password' => ['required', 'current_password'],
        'password' => ['required', 'string', 'min:8', 'confirmed'],
    ];
}

test('password request passes with valid data', function () {
    $u = User::factory()->create(['password' => bcrypt('secret123')]);
    Auth::login($u);

    $data = [
        'current_password' => 'secret123',
        'password' => 'newpass123',
        'password_confirmation' => 'newpass123',
    ];

    $v = Validator::make($data, passwordRules());
    expect($v->passes())->toBeTrue();
});

test('password request fails with wrong current password', function () {
    $u = User::factory()->create(['password' => bcrypt('secret123')]);
    Auth::login($u);

    $data = [
        'current_password' => 'wrong',
        'password' => 'newpass123',
        'password_confirmation' => 'newpass123',
    ];

    $v = Validator::make($data, passwordRules());
    expect($v->fails())->toBeTrue()
        ->and($v->errors()->has('current_password'))->toBeTrue();
});

test('password request fails when confirmation does not match', function () {
    $u = User::factory()->create(['password' => bcrypt('secret123')]);
    Auth::login($u);

    $data = [
        'current_password' => 'secret123',
        'password' => 'newpass123',
        'password_confirmation' => 'mismatch',
    ];

    $v = Validator::make($data, passwordRules());
    expect($v->fails())->toBeTrue()
        ->and($v->errors()->has('password'))->toBeTrue();
});
