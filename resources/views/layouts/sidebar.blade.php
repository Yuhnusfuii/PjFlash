@php
    $is = fn($pattern) => request()->routeIs($pattern) ? 'bg-sky-50 text-sky-700 dark:bg-slate-800' : '';
@endphp

<aside class="fixed left-0 top-[var(--topbar-h)] w-[var(--sidebar-w)] h-[calc(100vh-var(--topbar-h))] border-r bg-white dark:bg-slate-900 overflow-y-auto">
    <nav class="p-3 space-y-1 text-sm">
        <a href="{{ route('dashboard') }}"
           class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-800 {{ $is('dashboard') }}">
            <span>🏠</span><span>Dashboard</span>
        </a>

        <a href="{{ route('decks.index') }}"
           class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-800 {{ $is('decks.*') }}">
            <span>📁</span><span>Decks</span>
        </a>

        <a href="{{ route('items.index') }}"
           class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-800 {{ $is('items.index') }}">
            <span>🗂️</span><span>Items</span>
        </a>

        {{-- Explore (public decks) --}}
        <a href="{{ route('explore.index') }}"
           class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-800 {{ $is('explore.*') }}">
            {{-- icon compass --}}
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 opacity-70" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path d="M12 2v2m0 16v2m10-10h-2M4 12H2m15.36-6.36l-1.42 1.42M8.05 16.95l-1.41 1.41m10.62 0l-1.41-1.41M8.05 7.05L6.64 5.64" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M14 10l-4 4l-2-6l6 2z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <span>Explore</span>
        </a>

        <a href="{{ route('analytics.index') }}"
           class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-800 {{ $is('analytics.*') }}">
            <span>📊</span><span>Analytics</span>
        </a>

        <a href="{{ route('mcq.home') }}"
           class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-800 {{ $is('mcq.home') }}">
            <span>❓</span><span>MCQ</span>
        </a>

        <a href="{{ route('mcq.history') }}"
           class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-800 {{ $is('mcq.history') }}">
            <span>🕘</span><span>MCQ History</span>
        </a>

        <a href="{{ route('settings') }}"
           class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-800 {{ $is('settings') }}">
            <span>⚙️</span><span>Settings</span>
        </a>
    </nav>
</aside>
