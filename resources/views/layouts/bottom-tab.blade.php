<nav aria-label="Bottom Tabs" class="md:hidden fixed bottom-0 inset-x-0 z-30 bg-white/90 dark:bg-slate-900/90 backdrop-blur border-t border-slate-200 dark:border-slate-700">
  <div class="grid grid-cols-4">
    <a href="{{ route('home') }}" class="flex flex-col items-center justify-center py-2 text-xs {{ request()->routeIs('home') ? 'text-brand font-medium' : 'text-slate-600 dark:text-slate-300' }}">🏠<span>Home</span></a>

    @auth
      <a href="{{ route('decks.index') }}" class="flex flex-col items-center justify-center py-2 text-xs {{ request()->is('decks*') ? 'text-brand font-medium' : 'text-slate-600 dark:text-slate-300' }}">🗂️<span>Decks</span></a>
      <a href="{{ route('study.queue') }}" class="flex flex-col items-center justify-center py-2 text-xs {{ request()->is('study*') ? 'text-brand font-medium' : 'text-slate-600 dark:text-slate-300' }}">🎯<span>Study</span></a>
      <a href="{{ route('settings') }}" class="flex flex-col items-center justify-center py-2 text-xs {{ request()->is('settings*') ? 'text-brand font-medium' : 'text-slate-600 dark:text-slate-300' }}">⚙️<span>Settings</span></a>
    @else
      <a href="{{ route('login') }}" class="flex flex-col items-center justify-center py-2 text-xs text-slate-600 dark:text-slate-300">🔐<span>Login</span></a>
      <a href="{{ route('register') }}" class="flex flex-col items-center justify-center py-2 text-xs text-slate-600 dark:text-slate-300">➕<span>Sign up</span></a>
      <span></span>
    @endauth
  </div>
</nav>
