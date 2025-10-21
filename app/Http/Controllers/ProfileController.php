<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileAvatarRequest;
use App\Http\Requests\ProfilePasswordRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth'); // web guard
    }

    /** Trang Profile */
    public function edit(Request $request)
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /** Cập nhật thông tin cơ bản (name, email) */
    public function update(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'email' => [
                'required', 'string', 'lowercase', 'email', 'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
        ]);

        // Nếu đổi email -> hủy verify theo chuẩn Breeze
        if ($data['email'] !== $user->email) {
            $user->forceFill([
                'name' => $data['name'],
                'email' => $data['email'],
                'email_verified_at' => null,
            ])->save();
        } else {
            $user->update([
                'name' => $data['name'],
                'email' => $data['email'],
            ]);
        }

        return redirect()->route('profile.edit')->with('status', 'profile-updated');
    }

    /** Đổi mật khẩu (current_password + new password confirmed) */
    public function updatePassword(ProfilePasswordRequest $request)
    {
        $user = $request->user();

        $user->update([
            'password' => Hash::make($request->validated()['password']),
        ]);

        return redirect()->route('profile.edit')->with('status', 'password-updated');
    }

    /** Cập nhật avatar (lưu public storage, xóa ảnh cũ nếu có) */
        public function updateAvatar(ProfileAvatarRequest $request)
        {
            $user = $request->user();

            // file input name="avatar"
            $file = $request->file('avatar');
            if (! $file) {
                return back()->withErrors(['avatar' => 'Không nhận được tệp tải lên.']);
            }

            // lưu vào disk public => storage/app/public/avatars/xxx.jpg
            $path = $file->store('avatars', 'public');

            // xóa ảnh cũ (nếu có)
            if ($user->avatar_path && Storage::disk('public')->exists($user->avatar_path)) {
                Storage::disk('public')->delete($user->avatar_path);
            }

            // lưu DB (cần fillable avatar_path)
            $user->update(['avatar_path' => $path]);

            return redirect()->route('profile.edit')->with('status', 'avatar-updated');
        }

    /** Xóa tài khoản (yêu cầu password hiện tại) */
    public function destroy(Request $request)
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        // Xóa user
        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Test mặc định expect redirect('/')
        return redirect('/');
    }
}
