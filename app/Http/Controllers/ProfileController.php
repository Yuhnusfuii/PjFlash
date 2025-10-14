<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Http\Requests\ProfilePasswordRequest;
use App\Http\Requests\ProfileAvatarRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    /** GET /profile */
    public function edit(Request $request)
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /** PATCH /profile */
    public function update(ProfileUpdateRequest $request)
    {
        $request->user()->update($request->validated());
        return back()->with('status', 'Profile updated.');
    }

    /** PATCH /profile/password */
    public function updatePassword(ProfilePasswordRequest $request)
    {
        $user = $request->user();
        $user->update([
            'password' => Hash::make($request->validated()['password']),
        ]);

        return back()->with('status', 'Password changed.');
    }

    /** POST /profile/avatar */
    public function updateAvatar(ProfileAvatarRequest $request)
    {
        $user = $request->user();

        $file = $request->file('avatar');
        $path = $file->store('avatars', 'public'); // storage/app/public/avatars/...

        // Nếu có avatar cũ, xoá file cũ (nếu tồn tại)
        if ($user->avatar_path && Storage::disk('public')->exists($user->avatar_path)) {
            Storage::disk('public')->delete($user->avatar_path);
        }

        $user->update([
            'avatar_path' => $path, // cột string nullable trong bảng users
        ]);

        return back()->with('status', 'Avatar updated.');
    }
}
