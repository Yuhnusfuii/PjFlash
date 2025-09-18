@auth
<aside class="hidden lg:flex lg:flex-col lg:w-64 lg:shrink-0 bg-white dark:bg-slate-900 border-r border-slate-200 dark:border-slate-700">
  <div class="p-4">
    <nav class="space-y-1">
      <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-2 rounded-2xl hover:bg-slate-100 dark:hover:bg-slate-800 {{ request()->routeIs('dashboard') ? 'bg-slate-100 dark:bg-slate-800 text-brand' : '' }}">🏠 Dashboard</a>
      <a href="{{ route('decks.index') }}" class="flex items-center gap-3 px-4 py-2 rounded-2xl hover:bg-slate-100 dark:hover:bg-slate-800 {{ request()->is('decks*') ? 'bg-slate-100 dark:bg-slate-800 text-brand' : '' }}">🗂️ Decks</a>
      <a href="{{ route('items.index') }}" class="flex items-center gap-3 px-4 py-2 rounded-2xl hover:bg-slate-100 dark:hover:bg-slate-800 {{ request()->is('items*') ? 'bg-slate-100 dark:bg-slate-800 text-brand' : '' }}">📇 Items</a>
      <a href="{{ route('analytics.index') }}" class="flex items-center gap-3 px-4 py-2 rounded-2xl hover:bg-slate-100 dark:hover:bg-slate-800 {{ request()->is('analytics*') ? 'bg-slate-100 dark:bg-slate-800 text-brand' : '' }}">📊 Analytics</a>
      <a href="{{ route('settings') }}" class="flex items-center gap-3 px-4 py-2 rounded-2xl hover:bg-slate-100 dark:hover:bg-slate-800 {{ request()->is('settings*') ? 'bg-slate-100 dark:bg-slate-800 text-brand' : '' }}">⚙️ Settings</a>
      <li class="mt-2">
  <form method="POST" action="{{ route('logout') }}">
    @csrf
    <button type="submit"
      class="w-full flex items-center gap-2 px-3 py-2 rounded-xl text-left hover:bg-slate-100 dark:hover:bg-slate-800">
      <span class="i-lucide-log-out"></span>
      <span>Đăng xuất</span>
    </button>
  </form>
</li>

    </nav>
  </div>
</aside>
@endauth
