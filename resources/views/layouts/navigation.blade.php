@php
    $user = auth()->user();
    $avatarUrl = $user?->avatar_path ? asset('storage/'.$user->avatar_path) : null;

    $initials = '';
    if ($user?->name) {
        $parts = preg_split('/\s+/', trim($user->name));
        $initials = mb_strtoupper(mb_substr($parts[0] ?? '', 0, 1) . mb_substr(end($parts) ?: '', 0, 1));
    }
@endphp

<nav class="fixed inset-x-0 top-0 z-40 h-[var(--topbar-h)] border-b bg-white/80 dark:bg-slate-900/80 backdrop-blur">
    <div class="container-app flex h-full items-center justify-between gap-3">

        {{-- Left: burger + brand + desktop links --}}
        <div class="flex items-center gap-3">
            {{-- Mobile burger (JS trong app.blade bắt data-mobile-toggle) --}}
            <button
                class="md:hidden inline-flex h-10 w-10 items-center justify-center rounded-lg border text-slate-600 dark:text-slate-200"
                data-mobile-toggle
                aria-label="Open menu"
                aria-expanded="false">
                <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M4 6h16M4 12h16M4 18h16" stroke-linecap="round"/>
                </svg>
            </button>

            {{-- Brand --}}
            <a href="{{ route('home') }}" class="flex select-none items-center gap-2">
                <img src="{{ asset('icons/logo light mode.png') }}" alt="Yuhstud" class="h-7 dark:hidden">
                <img src="{{ asset('icons/logo dark mode.png') }}"  alt="Yuhstud" class="hidden h-7 dark:inline">
                <span class="sr-only">Yuhstud</span>
            </a>

            {{-- Desktop links --}}
            <ul class="ml-4 hidden items-center gap-4 text-sm md:flex text-slate-600 dark:text-slate-300">
                <li><a class="hover:underline" href="{{ route('dashboard') }}">Dashboard</a></li>
                <li><a class="hover:underline" href="{{ route('decks.index') }}">Decks</a></li>
                <li><a class="hover:underline" href="{{ route('items.index') }}">Items</a></li>
                <li><a class="hover:underline" href="{{ route('explore.index') }}">Explore</a></li>
                <li><a class="hover:underline" href="{{ route('analytics.index') }}">Analytics</a></li>
            </ul>
        </div>

        {{-- Right: theme toggle + avatar + logout --}}
        <div class="flex items-center gap-3">
            <button type="button" data-theme-toggle
                class="inline-flex h-9 w-9 items-center justify-center rounded-full border text-slate-700 dark:text-slate-200"
                title="Toggle theme">
                <svg viewBox="0 0 24 24" class="h-5 w-5" aria-hidden="true">
                    <g class="icon-sun" fill="currentColor">
                        <circle cx="12" cy="12" r="4"></circle>
                        <g stroke="currentColor" stroke-width="1.5" fill="none">
                            <line x1="12" y1="2"  x2="12" y2="5"></line>
                            <line x1="12" y1="19" x2="12" y2="22"></line>
                            <line x1="4.93" y1="4.93" x2="6.76" y2="6.76"></line>
                            <line x1="17.24" y1="17.24" x2="19.07" y2="19.07"></line>
                            <line x1="2" y1="12" x2="5" y2="12"></line>
                            <line x1="19" y1="12" x2="22" y2="12"></line>
                            <line x1="4.93" y1="19.07" x2="6.76" y2="17.24"></line>
                            <line x1="17.24" y1="6.76" x2="19.07" y2="4.93"></line>
                        </g>
                    </g>
                    <g class="icon-moon" fill="currentColor">
                        <path d="M21 12.79A9 9 0 1 1 11.21 3a7 7 0 0 0 9.79 9.79Z"></path>
                    </g>
                </svg>
            </button>

            {{-- Avatar → Profile --}}
            <a href="{{ route('profile.edit') }}"
               class="h-9 w-9 overflow-hidden rounded-full border focus:outline-none focus:ring-2 focus:ring-sky-500"
               title="{{ $user?->name }}">
                @if ($avatarUrl)
                    <img src="{{ $avatarUrl }}" alt="Avatar" class="h-full w-full object-cover" loading="lazy">
                @else
                    <div class="grid h-full w-full place-content-center bg-slate-200 text-xs font-semibold text-slate-700 dark:bg-slate-700 dark:text-slate-200">
                        {{ $initials ?: '🙂' }}
                    </div>
                @endif
            </a>

            {{-- Logout --}}
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="h-9 rounded-full border px-3 text-sm hover:bg-slate-50 dark:hover:bg-slate-800">
                    Đăng xuất
                </button>
            </form>
        </div>
    </div>
</nav>

@push('head')
<style>
  .icon-sun{display:inline}
  .icon-moon{display:none}
  .dark .icon-sun{display:none}
  .dark .icon-moon{display:inline}
</style>
@endpush
