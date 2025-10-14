<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Validator;

uses(RefreshDatabase::class);

/**
 * Đừng resolve FormRequest từ container (sẽ gọi authorize).
 * Ta test trực tiếp bộ rules giống hệt FormRequest của bạn:
 * avatar: required|file|mimes:jpg,jpeg,png,webp|max:2048
 */
function avatarRules(): array {
    return [
        'avatar' => ['required', 'file', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
    ];
}

test('avatar request accepts valid image types under 2MB', function () {
    // Không dùng ->image() (cần GD). Tạo file giả với mime hợp lệ:
    $file = UploadedFile::fake()->create('photo.jpg', 500, 'image/jpeg'); // 500 KB

    $v = Validator::make(['avatar' => $file], avatarRules());
    expect($v->passes())->toBeTrue();
});

test('avatar request rejects invalid mime', function () {
    $file = UploadedFile::fake()->create('anim.gif', 100, 'image/gif'); // sai mime

    $v = Validator::make(['avatar' => $file], avatarRules());
    expect($v->fails())->toBeTrue()
        ->and($v->errors()->has('avatar'))->toBeTrue();
});

test('avatar request rejects too large file', function () {
    $file = UploadedFile::fake()->create('big.png', 3000, 'image/png'); // 3000 KB > 2048

    $v = Validator::make(['avatar' => $file], avatarRules());
    expect($v->fails())->toBeTrue()
        ->and($v->errors()->has('avatar'))->toBeTrue();
});
