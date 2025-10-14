<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold">Explore public decks</h1>
        <div class="w-64">
            <input type="text" wire:model.live="q" class="w-full border rounded-lg px-3 py-2" placeholder="Search public decks...">
        </div>
    </div>

    <div class="grid md:grid-cols-3 gap-4">
        @forelse ($decks as $deck)
            <a href="{{ route('explore.show', $deck->slug) }}" class="card p-4 hover:shadow transition">
                <div class="font-semibold">{{ $deck->name }}</div>
                <div class="text-sm text-slate-500 mt-1">
                    {{ $deck->items_count }} items
                    @if (isset($deck->user))
                        • by {{ $deck->user->name }}
                    @endif
                </div>
                @if ($deck->description)
                    <div class="text-sm mt-2 line-clamp-2">{{ $deck->description }}</div>
                @endif
            </a>
        @empty
            <div class="text-slate-500">No public decks yet.</div>
        @endforelse
    </div>

    <div>
        {{ $decks->links() }}
    </div>
</div>
