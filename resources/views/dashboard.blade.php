{{-- resources/views/dashboard.blade.php --}}
<x-app-layout>
  @php
    $uid       = auth()->id();
    $deckCount = \App\Models\Deck::where('user_id',$uid)->count();
    $itemCount = \App\Models\Item::whereHas('deck', fn($q)=>$q->where('user_id',$uid))->count();
    $dueToday  = \App\Models\ReviewState::where('user_id',$uid)->where('due_at','<=',now())->count();
    $decks     = \App\Models\Deck::withCount('items')->where('user_id',$uid)->latest()->take(8)->get();
  @endphp

  <div class="space-y-6">
    {{-- Title --}}
    <div class="flex items-center justify-between">
      <h1 class="text-2xl font-bold">Dashboard</h1>
    </div>

    {{-- Metrics --}}
    <div class="grid gap-4 md:grid-cols-3">
      <x-ui-card class="y-card-pad">
        <div class="text-sm text-slate-500 dark:text-slate-400">Due today</div>
        <div class="text-3xl font-semibold mt-1">{{ $dueToday }}</div>
      </x-ui-card>

      <x-ui-card class="y-card-pad">
        <div class="text-sm text-slate-500 dark:text-slate-400">Total decks</div>
        <div class="text-3xl font-semibold mt-1">{{ $deckCount }}</div>
      </x-ui-card>

      <x-ui-card class="y-card-pad">
        <div class="text-sm text-slate-500 dark:text-slate-400">Total items</div>
        <div class="text-3xl font-semibold mt-1">{{ $itemCount }}</div>
      </x-ui-card>
    </div>

    {{-- Latest decks --}}
    <x-ui-card>
      <div class="y-card-pad">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-semibold">Your latest decks</h3>
          <a href="{{ route('decks.index') }}" class="y-btn border border-[var(--card-br)]">All decks</a>
        </div>

        @if($decks->isEmpty())
          <x-ui-alert class="mb-2">Bạn chưa có deck nào. Hãy tạo một deck mới nhé!</x-ui-alert>
        @else
          <ul class="divide-y divide-slate-200 dark:divide-slate-700">
            @foreach($decks as $d)
              <li class="py-3 flex items-center justify-between">
                <div>
                  <div class="font-semibold">{{ $d->name }}</div>
                  <div class="text-sm text-slate-500 dark:text-slate-400">{{ $d->items_count }} items</div>
                </div>
                <a href="{{ route('decks.show', $d) }}" class="y-btn y-btn--brand">Open</a>
              </li>
            @endforeach
          </ul>
        @endif
      </div>
    </x-ui-card>
  </div>
</x-app-layout>
