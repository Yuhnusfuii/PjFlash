@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto p-6 space-y-8">
    @if (session('status'))
        <div class="p-3 rounded bg-green-100 text-green-800">
            {{ session('status') }}
        </div>
    @endif

    <h1 class="text-2xl font-semibold">Profile</h1>

    {{-- Profile Info --}}
    <section class="border rounded-xl p-5">
        <h2 class="font-semibold mb-3">Basic info</h2>
        <form method="POST" action="{{ route('profile.update') }}" class="space-y-3">
            @csrf
            @method('PATCH')

            <div>
                <label class="block text-sm mb-1">Name</label>
                <input name="name" value="{{ old('name', $user->name) }}" class="w-full border rounded px-3 py-2">
                @error('name') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
            </div>

            {{-- Nếu muốn sửa email, mở comment + thêm rule ở ProfileUpdateRequest
            <div>
                <label class="block text-sm mb-1">Email</label>
                <input name="email" value="{{ old('email', $user->email) }}" class="w-full border rounded px-3 py-2">
                @error('email') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
            </div>
            --}}

            <button class="px-4 py-2 rounded bg-black text-white hover:opacity-90">Save</button>
        </form>
    </section>

    {{-- Avatar --}}
    <section class="border rounded-xl p-5">
        <h2 class="font-semibold mb-3">Avatar</h2>

        <div class="flex items-center gap-4 mb-3">
            <img
                src="{{ $user->avatar_path ? asset('storage/'.$user->avatar_path) : 'https://via.placeholder.com/80x80?text=Avatar' }}"
                alt="Avatar"
                class="w-20 h-20 rounded-full object-cover border"
            >
            @if($user->avatar_path)
                <a href="{{ asset('storage/'.$user->avatar_path) }}" target="_blank"
                   class="text-sm text-gray-600 underline">View full</a>
            @endif
        </div>

        <form method="POST" action="{{ route('profile.avatar') }}" enctype="multipart/form-data" class="space-y-3">
            @csrf
            <div>
                <input type="file" name="avatar" accept="image/png,image/jpeg,image/webp"
                       class="block w-full text-sm file:mr-3 file:px-3 file:py-2 file:rounded file:border-0 file:bg-gray-900 file:text-white hover:file:bg-black/90">
                @error('avatar') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
            </div>
            <button class="px-4 py-2 rounded bg-black text-white hover:opacity-90">Upload</button>
        </form>

        <p class="text-xs text-gray-500 mt-2">Max 2MB. Types: JPG, JPEG, PNG, WEBP.</p>
    </section>

    {{-- Change password --}}
    <section class="border rounded-xl p-5">
        <h2 class="font-semibold mb-3">Change password</h2>
        <form method="POST" action="{{ route('profile.password') }}" class="space-y-3">
            @csrf
            @method('PATCH')

            <div>
                <label class="block text-sm mb-1">Current password</label>
                <input type="password" name="current_password" class="w-full border rounded px-3 py-2" autocomplete="current-password">
                @error('current_password') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm mb-1">New password</label>
                <input type="password" name="password" class="w-full border rounded px-3 py-2" autocomplete="new-password">
                @error('password') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm mb-1">Confirm new password</label>
                <input type="password" name="password_confirmation" class="w-full border rounded px-3 py-2" autocomplete="new-password">
            </div>

            <button class="px-4 py-2 rounded bg-black text-white hover:opacity-90">Update password</button>
        </form>
    </section>
</div>
@endsection
