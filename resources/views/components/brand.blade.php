@props(['class' => ''])

<a href="{{ route('home') }}" class="inline-flex items-center gap-2 {{ $class }}">
    {{-- logo light/tối: dùng tailwind dark: để ẩn/hiện --}}
    <img
        src="{{ asset('brand/yuhstud-light.png') }}"
        alt="Yuhstud"
        class="h-7 dark:hidden select-none"
        draggable="false"
    >
    <img
        src="{{ asset('brand/yuhstud-dark.png') }}"
        alt="Yuhstud"
        class="h-7 hidden dark:inline select-none"
        draggable="false"
    >
    {{-- text fallback (ẩn với screen) hoặc nếu muốn có chữ cạnh logo thì bỏ sr-only --}}
    <span class="sr-only">{{ config('app.name', 'Yuhstud') }}</span>
</a>
