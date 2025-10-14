@php
    $user = auth()->user();
    $avatarUrl = $user?->avatar_path ? asset('storage/'.$user->avatar_path) : null;
    $initials = '';
    if ($user?->name) {
        $parts = preg_split('/\s+/', trim($user->name));
        $initials = mb_strtoupper(mb_substr($parts[0] ?? '', 0, 1) . mb_substr(end($parts) ?: '', 0, 1));
    }
@endphp

<nav class="h-[var(--topbar-h)] border-b bg-white/80 dark:bg-slate-900/80 backdrop-blur fixed top-0 left-0 right-0 z-40">
    <div class="flex items-center justify-between h-full gap-4 container-app">
        <div class="flex items-center gap-6">
            <a href="{{ route('home') }}" class="flex items-center gap-2 font-semibold">
                <span class="text-sky-500">🧠</span>
                <span>PjFlash</span>
            </a>

            {{-- Main menu (desktop) --}}
            <ul class="items-center hidden gap-4 text-sm md:flex text-slate-600 dark:text-slate-300">
                <li><a href="{{ route('home') }}" class="hover:underline">Home</a></li>
                <li><a href="{{ route('dashboard') }}" class="hover:underline">Dashboard</a></li>
                <li><a href="{{ route('decks.index') }}" class="hover:underline">Decks</a></li>
                <li><a href="{{ route('items.index') }}" class="hover:underline">Items</a></li>

                {{-- Explore (public decks) --}}
                <li><a href="{{ route('explore.index') }}" class="hover:underline">Explore</a></li>

                <li><a href="{{ route('analytics.index') }}" class="hover:underline">Analytics</a></li>
            </ul>
        </div>

        <div class="flex items-center gap-3">
            {{-- Theme toggle (giữ nguyên) --}}
            <button type="button" data-theme-toggle
                    class="theme-btn text-slate-600 dark:text-slate-200"
                    title="Toggle theme" aria-label="Toggle theme" aria-pressed="{{ app()->isProduction() ? 'false' : (session('theme') === 'dark' ? 'true' : 'false') }}">
                <svg viewBox="0 0 24 24" class="theme-icon" aria-hidden="true">
                    {{-- Sun --}}
                    <g class="sun" fill="currentColor">
                        <circle cx="12" cy="12" r="4"></circle>
                        <g stroke="currentColor" stroke-width="1.5" fill="none">
                            <line x1="12" y1="2" x2="12" y2="5"></line>
                            <line x1="12" y1="19" x2="12" y2="22"></line>
                            <line x1="4.93" y1="4.93" x2="6.76" y2="6.76"></line>
                            <line x1="17.24" y1="17.24" x2="19.07" y2="19.07"></line>
                            <line x1="2" y1="12" x2="5" y2="12"></line>
                            <line x1="19" y1="12" x2="22" y2="12"></line>
                            <line x1="4.93" y1="19.07" x2="6.76" y2="17.24"></line>
                            <line x1="17.24" y1="6.76" x2="19.07" y2="4.93"></line>
                        </g>
                    </g>
                    {{-- Moon --}}
                    <g class="moon" fill="currentColor">
                        <path d="M21 12.79A9 9 0 1 1 11.21 3a7 7 0 0 0 9.79 9.79Z"></path>
                    </g>
                </svg>
            </button>

            {{-- Avatar --}}
            <a href="{{ route('profile.edit') }}"
               class="overflow-hidden border rounded-full h-9 w-9 ring-0 focus:outline-none focus:ring-2 focus:ring-sky-500"
               title="{{ $user?->name }}">
                @if ($avatarUrl)
                    <img src="{{ $avatarUrl }}" alt="Avatar" class="object-cover w-full h-full" loading="lazy">
                @else
                    <div class="grid w-full h-full text-xs font-semibold place-content-center bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-200">
                        {{ $initials ?: '🙂' }}
                    </div>
                @endif
            </a>

            {{-- Logout --}}
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="px-3 text-sm border rounded-full h-9 hover:bg-slate-50 dark:hover:bg-slate-800">
                    Đăng xuất
                </button>
            </form>
        </div>
    </div>
</nav>
