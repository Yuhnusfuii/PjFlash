{{-- resources/views/dashboard.blade.php --}}
@push('styles')
<style>
  /* CSS riêng cho trang này (tuỳ chọn) */
  .hero { background: radial-gradient(1200px 600px at 10% 0%, #e0f2fe 0, transparent 60%); }
</style>
@endpush

<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl leading-tight">Dashboard</h2>
  </x-slot>

  @php
    $uid = auth()->id();
    $deckCount = \App\Models\Deck::where('user_id',$uid)->count();
    $itemCount = \App\Models\Item::whereHas('deck', fn($q)=>$q->where('user_id',$uid))->count();
    $dueToday  = \App\Models\ReviewState::where('user_id',$uid)->where('due_at','<=',now())->count();
    $decks     = \App\Models\Deck::withCount('items')->where('user_id',$uid)->latest()->take(8)->get();
  @endphp

  <div class="container-app py-6 space-y-6">
    <div class="grid gap-4 md:grid-cols-3">
      <div class="card p-6">
        <div class="text-sm text-slate-500">Due today</div>
        <div class="text-3xl font-semibold">{{ $dueToday }}</div>
      </div>
      <div class="card p-6">
        <div class="text-sm text-slate-500">Total decks</div>
        <div class="text-3xl font-semibold">{{ $deckCount }}</div>
      </div>
      <div class="card p-6">
        <div class="text-sm text-slate-500">Total items</div>
        <div class="text-3xl font-semibold">{{ $itemCount }}</div>
      </div>
    </div>

    <div class="card p-6">
      <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-medium">Your latest decks</h3>
        <a href="{{ url('/decks') }}" class="btn-outline">All decks</a>
      </div>
      <ul class="divide-y divide-slate-200 dark:divide-slate-700">
        @forelse($decks as $d)
          <li class="py-3 flex items-center justify-between">
            <div>
              <div class="font-semibold">{{ $d->name }}</div>
              <div class="text-sm text-slate-500">{{ $d->items_count }} items</div>
            </div>
          </li>
        @empty
          <li class="py-6 text-slate-500">No decks yet.</li>
        @endforelse
      </ul>
    </div>
  </div>
</x-app-layout>


@push('scripts')
<script>
  // JS riêng cho Dashboard (nếu cần)
</script>
@endpush
