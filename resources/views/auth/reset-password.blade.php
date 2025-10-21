{{-- resources/views/auth/reset-password.blade.php --}}
<x-guest-layout>
    <div class="w-full max-w-md mx-auto">
        <div class="card p-6 md:p-8 shadow-sm">
            <div class="flex items-center gap-3 mb-4">
                <div class="h-10 w-10 rounded-2xl bg-sky-100 dark:bg-sky-900/40 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-sky-600 dark:text-sky-300" viewBox="0 0 24 24" fill="currentColor"><path d="M12 1a9 9 0 00-9 9v3.586l-1.707 1.707A1 1 0 003 17h18a1 1 0 00.707-1.707L20 13.586V10a9 9 0 00-9-9zm0 6a3 3 0 013 3H9a3 3 0 013-3zM5 19a2 2 0 002 2h10a2 2 0 002-2H5z"/></svg>
                </div>
                <h1 class="text-xl font-semibold">Đặt lại mật khẩu</h1>
            </div>

            <form method="POST" action="{{ route('password.store') }}" class="space-y-4">
                @csrf
                <input type="hidden" name="token" value="{{ request()->route('token') }}">

                <div>
                    <label for="email" class="block text-sm font-medium mb-1">Email</label>
                    <input id="email" name="email" type="email" required
                           value="{{ old('email', request('email')) }}"
                           class="w-full rounded-lg border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 focus:border-sky-400 focus:ring-sky-400">
                    @error('email')
                        <p class="mt-1 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium mb-1">Mật khẩu mới</label>
                    <input id="password" name="password" type="password" required
                           class="w-full rounded-lg border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 focus:border-sky-400 focus:ring-sky-400">
                    @error('password')
                        <p class="mt-1 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium mb-1">Xác nhận mật khẩu</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" required
                           class="w-full rounded-lg border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 focus:border-sky-400 focus:ring-sky-400">
                </div>

                <div class="flex items-center gap-3 pt-2">
                    <button type="submit" class="btn btn-primary">
                        Đặt lại mật khẩu
                    </button>

                    <a href="{{ route('login') }}" class="btn btn-outline">
                        Về đăng nhập
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>
