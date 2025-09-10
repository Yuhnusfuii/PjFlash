<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\GoogleProvider; // 👈 quan trọng để IDE nhận method

class GoogleController extends Controller
{
    public function redirect(): RedirectResponse
    {
        /** @var GoogleProvider $google */
        $google = Socialite::driver('google');

        // Có thể bỏ dòng này vì Google mặc định đã có 'openid','profile','email'
        $google->scopes(['openid','profile','email']);

        return $google->redirect();
    }

    public function callback(): RedirectResponse
    {
        /** @var GoogleProvider $google */
        $google = Socialite::driver('google');

        try {
            // stateful trước (ưu tiên an toàn)
            $googleUser = $google->user();
        } catch (\Throwable $e) {
            // fallback nếu gặp InvalidState
            $google->stateless();
            $googleUser = $google->user();
        }

        $email = $googleUser->getEmail();
        if (empty($email)) {
            return redirect()->route('login')
                ->withErrors(['email' => 'Google không trả về email. Hãy thử tài khoản khác.']);
        }

        $user = User::where('email', $email)->first();

        if ($user) {
            if (!$user->google_id) {
                $user->google_id = $googleUser->getId();
                $user->avatar    = $googleUser->getAvatar();
                $user->save();
            }
        } else {
            $user = User::create([
                'name'              => $googleUser->getName() ?: $googleUser->getNickname() ?: 'User',
                'email'             => $email,
                'google_id'         => $googleUser->getId(),
                'avatar'            => $googleUser->getAvatar(),
                'password'          => bcrypt(Str::random(32)),
                'email_verified_at' => $this->googleEmailVerified($googleUser) ? now() : null,
            ]);
        }

        Auth::login($user, true);
        return redirect()->intended(route('dashboard'));
    }

    private function googleEmailVerified($googleUser): bool
    {
        $flag = data_get($googleUser->user, 'email_verified');
        if ($flag === null) {
            $flag = data_get($googleUser->user, 'verified_email');
        }
        return (bool) $flag;
    }
}
