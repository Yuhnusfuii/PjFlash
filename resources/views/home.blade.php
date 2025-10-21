<x-guest-layout>
    <div class="text-center">
        <h1 class="text-3xl font-semibold">Yuhstud</h1>
        <p class="mt-2 text-slate-600 dark:text-slate-300">
            Ứng dụng luyện ghi nhớ bằng Flashcard &amp; Trắc nghiệm (PWA).
        </p>

        <div class="mt-6 flex items-center justify-center gap-3">
            <a href="{{ route('login') }}" class="btn btn-primary px-5">Đăng nhập</a>
            <a href="{{ route('register') }}" class="btn btn-outline px-5">Đăng ký</a>
        </div>

        <div class="mt-8 text-sm text-slate-500">
            Đăng nhập để vào Dashboard và bắt đầu học ngay 🚀
        </div>
    </div>
</x-guest-layout>
