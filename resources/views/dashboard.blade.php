@push('styles')
<style>
  .hero { background: radial-gradient(1200px 600px at 10% 0%, #e0f2fe 0, transparent 60%); }
</style>
@endpush

@extends('layouts.app')

@section('content')
<div class="py-6 space-y-6 container-app">

    <div class="p-6 border hero rounded-2xl">
        <h2 class="text-xl font-semibold leading-tight">Dashboard</h2>
    </div>

    {{-- Stats --}}
    <div class="grid gap-4 md:grid-cols-3">
        <div class="p-6 card">
            <div class="text-sm text-slate-500">Due today</div>
            <div class="text-3xl font-semibold">{{ $dueToday }}</div>
        </div>
        <div class="p-6 card">
            <div class="text-sm text-slate-500">Total decks</div>
            <div class="text-3xl font-semibold">{{ $deckCount }}</div>
        </div>
        <div class="p-6 card">
            <div class="text-sm text-slate-500">Total items</div>
            <div class="text-3xl font-semibold">{{ $itemCount }}</div>
        </div>
    </div>

    {{-- Latest decks: GRID --}}
    <div class="p-6 card">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-medium">Your latest decks</h3>
            <a href="{{ route('decks.index') }}" class="btn-outline">All decks</a>
        </div>

        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            @forelse($decks as $d)
                <div class="p-4 transition bg-white border rounded-xl hover:shadow-sm dark:bg-slate-900">
                    <div class="flex items-center gap-2 mb-1 text-xs text-slate-500">
                        <span>{{ $d->created_at?->diffForHumans() ?? '—' }}</span>
                        <span class="px-1.5 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                            {{ $d->items_count }} items
                        </span>
                    </div>
                    <h4 class="font-semibold line-clamp-2">{{ $d->name }}</h4>

                    @if($d->description)
                        <p class="mt-1 text-sm text-slate-600 dark:text-slate-300 line-clamp-2">{{ $d->description }}</p>
                    @endif

                    <div class="flex gap-2 mt-4">
                        <a href="{{ route('decks.show', ['deck' => $d->id]) }}"
                           class="px-3 py-1.5 rounded border hover:bg-slate-50 dark:hover:bg-slate-800 text-sm">Open</a>
                        <a href="{{ route('decks.study', ['deck' => $d->id]) }}"
                           class="px-3 py-1.5 rounded bg-black text-white text-sm">Study</a>
                    </div>
                </div>
            @empty
                <div class="text-slate-500">No decks yet.</div>
            @endforelse
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// JS riêng cho Dashboard (nếu cần)
</script>
@endpush
