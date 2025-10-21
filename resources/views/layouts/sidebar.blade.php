@php
    $isActive = fn(string $pattern)
        => request()->routeIs($pattern) ? 'bg-sky-50 text-sky-700 dark:bg-slate-800' : '';
@endphp

<aside
    class="fixed left-0 top-[var(--topbar-h)] z-30 h-[calc(100vh-var(--topbar-h))] w-[var(--sidebar-w)]
           border-r bg-white dark:bg-slate-900 overflow-y-auto
           hidden md:block"
    data-mobile-panel>
    <nav class="p-3 space-y-1 text-sm">
        <a href="{{ route('dashboard') }}"
           class="flex items-center gap-3 rounded-lg px-3 py-2 hover:bg-slate-50 dark:hover:bg-slate-800 {{ $isActive('dashboard') }}">
            <span>🏠</span><span>Dashboard</span>
        </a>
        <a href="{{ route('decks.index') }}"
           class="flex items-center gap-3 rounded-lg px-3 py-2 hover:bg-slate-50 dark:hover:bg-slate-800 {{ $isActive('decks.*') }}">
            <span>📁</span><span>Decks</span>
        </a>
        <a href="{{ route('items.index') }}"
           class="flex items-center gap-3 rounded-lg px-3 py-2 hover:bg-slate-50 dark:hover:bg-slate-800 {{ $isActive('items.index') }}">
            <span>🗂️</span><span>Items</span>
        </a>
        <a href="{{ route('explore.index') }}"
           class="flex items-center gap-3 rounded-lg px-3 py-2 hover:bg-slate-50 dark:hover:bg-slate-800 {{ $isActive('explore.*') }}">
            <span>🧭</span><span>Explore</span>
        </a>
        <a href="{{ route('analytics.index') }}"
           class="flex items-center gap-3 rounded-lg px-3 py-2 hover:bg-slate-50 dark:hover:bg-slate-800 {{ $isActive('analytics.*') }}">
            <span>📊</span><span>Analytics</span>
        </a>
        <a href="{{ route('mcq.home') }}"
           class="flex items-center gap-3 rounded-lg px-3 py-2 hover:bg-slate-50 dark:hover:bg-slate-800 {{ $isActive('mcq.*') }}">
            <span>❓</span><span>MCQ</span>
        </a>
        <a href="{{ route('settings') }}"
           class="flex items-center gap-3 rounded-lg px-3 py-2 hover:bg-slate-50 dark:hover:bg-slate-800 {{ $isActive('settings') }}">
            <span>⚙️</span><span>Settings</span>
        </a>
    </nav>
</aside>
