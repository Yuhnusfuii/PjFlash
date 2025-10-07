<div class="max-w-6xl p-6 mx-auto space-y-6">
    {{-- Flash --}}
    @if (session('ok'))
        <div class="p-3 text-green-800 bg-green-100 rounded">
            {{ session('ok') }}
        </div>
    @endif

    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-semibold">Decks</h1>
    </div>

    {{-- Search + Create --}}
    <div class="space-y-2">
        <input
            type="text"
            placeholder="Search decks..."
            class="w-full px-3 py-2 border rounded-lg"
            wire:model.live.debounce.300ms="q"
        />

        <div class="flex items-center gap-2">
            <input
                type="text"
                placeholder="New deck name..."
                class="flex-1 px-3 py-2 border rounded-lg"
                wire:model.defer="newName"
                wire:keydown.enter.prevent="createDeck"
            />
            <button
                wire:click="createDeck"
                class="px-4 py-2 text-white bg-black rounded hover:opacity-90">
                Create
            </button>
        </div>
        @error('newName') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    {{-- Deck cards --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @forelse ($decks as $d)
            <div class="p-4 transition border rounded-xl hover:shadow-sm">
                <div class="flex items-center gap-2 mb-1 text-xs text-gray-500">
                    <span>{{ $d->created_at?->diffForHumans() ?? '—' }}</span>
                    <span class="px-1.5 py-0.5 rounded bg-gray-100 text-gray-700">#{{ $d->items_count }}</span>
                </div>

                <h3 class="font-semibold">{{ $d->name }}</h3>
                @if($d->description)
                    <p class="mt-1 text-sm text-gray-600 line-clamp-2">{{ $d->description }}</p>
                @endif

                <div class="flex gap-2 mt-4">
                    <a href="{{ route('decks.show', ['deck' => $d->id]) }}"
                       class="px-3 py-1.5 rounded border hover:bg-gray-50 text-sm">Open</a>
                    <a href="{{ route('decks.study', ['deck' => $d->id]) }}"
                       class="px-3 py-1.5 rounded bg-black text-white text-sm">Study</a>
                    <a href="{{ route('decks.analytics', ['deck' => $d->id]) }}"
                       class="px-3 py-1.5 rounded border hover:bg-gray-50 text-sm">Analytics</a>
                </div>
            </div>
        @empty
            <div class="text-gray-600">No decks yet.</div>
        @endforelse
    </div>

    @if($decks->hasPages())
        <div>
            {{ $decks->links() }}
        </div>
    @endif
</div>
