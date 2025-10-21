<x-guest-layout>
    <h1 class="text-2xl font-semibold mb-6 text-center">Đăng nhập</h1>

    @if (session('status'))
        <div class="mb-4 rounded-lg border border-emerald-300 bg-emerald-50 px-3 py-2 text-emerald-700 dark:bg-emerald-900/30 dark:border-emerald-700">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <div>
            <label class="block text-sm mb-1">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" autofocus
                   class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-3 py-2 outline-none focus:ring-2 focus:ring-emerald-500" />
            @error('email') <p class="mt-1 text-sm text-rose-500">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm mb-1">Mật khẩu</label>
            <input id="password" type="password" name="password" autocomplete="current-password"
                   class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-3 py-2 outline-none focus:ring-2 focus:ring-emerald-500" />
            @error('password') <p class="mt-1 text-sm text-rose-500">{{ $message }}</p> @enderror
        </div>

        <div class="flex items-center justify-between">
            <label class="inline-flex items-center gap-2 text-sm">
                <input type="checkbox" name="remember" class="rounded border-slate-300 dark:border-slate-600">
                Ghi nhớ
            </label>
            <a href="{{ route('password.request') }}" class="text-sm text-sky-600 hover:underline">Quên mật khẩu?</a>
        </div>

        <button class="btn btn-primary w-full">Đăng nhập</button>
    </form>

    <div class="mt-4">
        <a href="{{ url('auth/google/redirect') }}" class="btn btn-outline w-full">
            <svg class="mr-2" width="18" height="18" viewBox="0 0 533.5 544.3"><path fill="#4285f4" d="M533.5 278.4..."/></svg>
            Sign in with Google
        </a>
    </div>

    <p class="mt-6 text-center text-sm text-slate-500">
        Chưa có tài khoản?
        <a href="{{ route('register') }}" class="text-sky-600 font-medium hover:underline">Đăng ký</a>
    </p>
</x-guest-layout>
