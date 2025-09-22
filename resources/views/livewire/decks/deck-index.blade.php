<div class="max-w-5xl p-6 mx-auto space-y-6">

    {{-- Flash message --}}
    @if (session('ok'))
        <div class="p-3 text-sm text-green-800 bg-green-100 rounded">{{ session('ok') }}</div>
    @endif

    {{-- Search box --}}
    <div class="flex items-center gap-3">
        <input
            type="text"
            placeholder="Search decks..."
            wire:model.live.debounce.300ms="q"
            class="w-full px-3 py-2 border rounded focus:outline-none focus:ring focus:ring-gray-200"
        >
        <a href="{{ route('decks.index', []) }}" class="text-sm underline">Reset</a>
    </div>

    {{-- Create deck --}}
    <div class="flex items-center gap-2">
        <input
            type="text"
            placeholder="New deck name..."
            wire:model="newName"
            class="w-full px-3 py-2 border rounded focus:outline-none focus:ring focus:ring-gray-200"
        >
        <button
            wire:click="createDeck"
            class="px-4 py-2 text-white bg-black rounded hover:opacity-90"
        >Create</button>
    </div>

    {{-- Deck list --}}
    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
        @forelse($decks as $deck)
            <a href="{{ route('decks.show', $deck) }}" class="p-4 transition border rounded hover:shadow">
                <div class="text-sm text-gray-500">{{ $deck->created_at?->diffForHumans() }}</div>
                <div class="mt-1 text-lg font-semibold line-clamp-2">{{ $deck->name }}</div>
                @if(!empty($deck->description))
                    <div class="mt-2 text-sm text-gray-600 line-clamp-2">{{ $deck->description }}</div>
                @endif
            </a>
        @empty
            <div class="text-gray-500 col-span-full">No decks found.</div>
        @endforelse
    </div>

    {{-- Pagination --}}
    <div>
        {{ $decks->links() }}
    </div>
</div>
