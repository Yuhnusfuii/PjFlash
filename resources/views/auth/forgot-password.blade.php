{{-- resources/views/auth/forgot-password.blade.php --}}
<x-guest-layout>
    <div class="w-full max-w-md mx-auto">
        <div class="card p-6 md:p-8 shadow-sm">
            <div class="flex items-center gap-3 mb-4">
                <div class="h-10 w-10 rounded-2xl bg-sky-100 dark:bg-sky-900/40 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-sky-600 dark:text-sky-300" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2a9 9 0 00-9 9v3.586l-1.707 1.707A1 1 0 003 18h18a1 1 0 00.707-1.707L20 14.586V11a9 9 0 00-9-9zM7 20a2 2 0 002 2h6a2 2 0 002-2H7z"/></svg>
                </div>
                <h1 class="text-xl font-semibold">Quên mật khẩu</h1>
            </div>

            <p class="text-sm text-slate-600 dark:text-slate-300 mb-4">
                Nhập email của bạn. Chúng tôi sẽ gửi một liên kết để đặt lại mật khẩu.
            </p>

            @if (session('status'))
                <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-700 dark:bg-emerald-900/30 dark:border-emerald-800 dark:text-emerald-200">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
                @csrf

                <div>
                    <label for="email" class="block text-sm font-medium mb-1">Email</label>
                    <input id="email" name="email" type="email" required autofocus
                           value="{{ old('email') }}"
                           class="w-full rounded-lg border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 focus:border-sky-400 focus:ring-sky-400">
                    @error('email')
                        <p class="mt-1 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center gap-3 pt-2">
                    <button type="submit" class="btn btn-primary">
                        Gửi liên kết đặt lại
                    </button>

                    <a href="{{ route('login') }}" class="btn btn-outline">
                        Quay lại đăng nhập
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>
