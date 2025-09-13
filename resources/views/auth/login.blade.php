<x-guest-layout>
  <div class="container-app py-10">
    <div class="max-w-md mx-auto card fade-in">
      <div class="card-body">
        <h1 class="text-xl font-semibold mb-4">Đăng nhập</h1>

        {{-- form gốc của Breeze giữ nguyên, chỉ thêm class .input/.btn --}}
        <form method="POST" action="{{ route('login') }}" class="space-y-4">
          @csrf
          @if (Route::has('oauth.google.redirect'))
          <div class="mt-4">
            <a href="{{ route('oauth.google.redirect') }}" class="btn-outline w-full text-center">
              Sign in with Google
            </a>
          </div>
          @endif
          <div>
            <label class="label" for="email">Email</label>
            <x-text-input id="email" class="input" type="email" name="email" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
          </div>

          <div>
            <label class="label" for="password">Mật khẩu</label>
            <x-text-input id="password" class="input" type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
          </div>

          <div class="flex items-center justify-between">
            <label class="inline-flex items-center gap-2">
              <input type="checkbox" name="remember" class="rounded border-slate-300 dark:border-slate-600">
              <span class="text-sm">Ghi nhớ</span>
            </label>

            <a class="text-sm text-brand hover:underline" href="{{ route('password.request') }}">Quên mật khẩu?</a>
          </div>

          <button class="btn w-full">Đăng nhập</button>

          <div class="mt-4">
            <a href="{{ route('oauth.google.redirect') }}" class="btn-outline w-full text-center">Sign in with Google</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</x-guest-layout>
