<x-guest-layout>
    <h1 class="text-2xl font-semibold mb-6 text-center">Tạo tài khoản</h1>

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        <div>
            <label class="block text-sm mb-1">Tên hiển thị</label>
            <input name="name" value="{{ old('name') }}" autofocus
                   class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-3 py-2 outline-none focus:ring-2 focus:ring-emerald-500"/>
            @error('name') <p class="mt-1 text-sm text-rose-500">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm mb-1">Email</label>
            <input type="email" name="email" value="{{ old('email') }}"
                   class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-3 py-2 outline-none focus:ring-2 focus:ring-emerald-500"/>
            @error('email') <p class="mt-1 text-sm text-rose-500">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm mb-1">Mật khẩu</label>
            <input type="password" name="password" autocomplete="new-password"
                   class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-3 py-2 outline-none focus:ring-2 focus:ring-emerald-500"/>
            @error('password') <p class="mt-1 text-sm text-rose-500">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm mb-1">Xác nhận mật khẩu</label>
            <input type="password" name="password_confirmation" autocomplete="new-password"
                   class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-3 py-2 outline-none focus:ring-2 focus:ring-emerald-500"/>
        </div>

        <button class="btn btn-primary w-full">Đăng ký</button>
    </form>

    <div class="mt-4">
        <a href="{{ url('auth/google/redirect') }}" class="btn btn-outline w-full">
            <svg class="mr-2" width="18" height="18" viewBox="0 0 533.5 544.3"><path fill="#4285f4" d="M533.5 278.4..."/></svg>
            Sign up with Google
        </a>
    </div>

    <p class="mt-6 text-center text-sm text-slate-500">
        Đã có tài khoản?
        <a href="{{ route('login') }}" class="text-sky-600 font-medium hover:underline">Đăng nhập</a>
    </p>
</x-guest-layout>
