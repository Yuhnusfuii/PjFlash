@php
  $is = fn($p) => request()->routeIs($p);
@endphp

<nav class="border-b border-slate-200 dark:border-slate-700 bg-white/80 dark:bg-slate-900/80 backdrop-blur">
  <div class="container-app h-16 flex items-center justify-between">
    {{-- Left --}}
    <div class="flex items-center gap-6">
      <a href="{{ route('home') }}" class="flex items-center gap-2 font-semibold">
        <x-application-logo class="h-6 w-6 text-brand" />
        <span>PjFlash</span>
      </a>

      <div class="hidden md:flex items-center gap-4">
        <a href="{{ route('home') }}" class="{{ $is('home') ? 'text-brand' : 'text-slate-600 dark:text-slate-300' }}">Home</a>
        @auth
          <a href="{{ route('dashboard') }}" class="{{ $is('dashboard') ? 'text-brand' : 'text-slate-600 dark:text-slate-300' }}">Dashboard</a>
          <a href="{{ route('decks.index') }}" class="{{ request()->is('decks*') ? 'text-brand' : 'text-slate-600 dark:text-slate-300' }}">Decks</a>
          <a href="{{ route('items.index') }}" class="{{ request()->is('items*') ? 'text-brand' : 'text-slate-600 dark:text-slate-300' }}">Items</a>
          <a href="{{ route('analytics.index') }}" class="{{ request()->is('analytics*') ? 'text-brand' : 'text-slate-600 dark:text-slate-300' }}">Analytics</a>
        @endauth
      </div>
    </div>

    {{-- Right --}}
    <div class="flex items-center gap-2">
      <button data-theme-toggle class="inline-flex items-center justify-center px-3 py-2 rounded-2xl hover:bg-slate-100 dark:hover:bg-slate-800" title="Toggle theme">🌙</button>

      <div class="hidden sm:flex sm:items-center sm:ml-6">
        @auth
          @includeWhen(View::exists('layouts.partials.user-dropdown'), 'layouts.partials.user-dropdown')
        @else
          <a href="{{ route('login') }}" class="btn-outline">Đăng nhập</a>
        @endauth
      </div>

      <button
        data-mobile-toggle
        class="md:hidden inline-flex items-center justify-center px-3 py-2 rounded-2xl hover:bg-slate-100 dark:hover:bg-slate-800"
        aria-expanded="false" aria-label="Open Menu">☰</button>
    </div>
  </div>
</nav>

{{-- ⚠️ Panel mobile phải là "ngay-sau" nav ở trên --}}
<div data-mobile-panel class="hidden md:hidden border-t border-slate-200 dark:border-slate-700">
  <div class="px-4 py-3 space-y-2">
    <a href="{{ route('home') }}" class="block px-3 py-2 rounded-2xl hover:bg-slate-100 dark:hover:bg-slate-800 {{ $is('home') ? 'bg-slate-100 dark:bg-slate-800 text-brand' : '' }}">Home</a>
    @auth
      <a href="{{ route('dashboard') }}" class="block px-3 py-2 rounded-2xl hover:bg-slate-100 dark:hover:bg-slate-800 {{ $is('dashboard') ? 'bg-slate-100 dark:bg-slate-800 text-brand' : '' }}">Dashboard</a>
      <a href="{{ route('decks.index') }}" class="block px-3 py-2 rounded-2xl hover:bg-slate-100 dark:hover:bg-slate-800 {{ request()->is('decks*') ? 'bg-slate-100 dark:bg-slate-800 text-brand' : '' }}">Decks</a>
      <a href="{{ route('items.index') }}" class="block px-3 py-2 rounded-2xl hover:bg-slate-100 dark:hover:bg-slate-800 {{ request()->is('items*') ? 'bg-slate-100 dark:bg-slate-800 text-brand' : '' }}">Items</a>
      <a href="{{ route('analytics.index') }}" class="block px-3 py-2 rounded-2xl hover:bg-slate-100 dark:hover:bg-slate-800 {{ request()->is('analytics*') ? 'bg-slate-100 dark:bg-slate-800 text-brand' : '' }}">Analytics</a>
    @else
      <a href="{{ route('login') }}" class="block px-3 py-2 rounded-2xl hover:bg-slate-100 dark:hover:bg-slate-800">Đăng nhập</a>
    @endauth
  </div>
</div>
