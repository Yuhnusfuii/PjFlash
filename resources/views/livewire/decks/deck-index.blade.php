<div class="max-w-6xl mx-auto p-6 space-y-6">

    {{-- Flash --}}
    @if (session('ok'))
        <div class="p-3 rounded bg-green-100 text-green-800 text-sm">{{ session('ok') }}</div>
    @endif

    {{-- Search --}}
    <div class="flex items-center gap-3">
        <input
            type="text"
            placeholder="Search decks..."
            wire:model.live.debounce.300ms="q"
            class="border rounded px-3 py-2 w-full focus:outline-none focus:ring focus:ring-gray-200"
        >
        <a href="{{ route('decks.index') }}" class="text-sm underline">Reset</a>
    </div>

    {{-- Create --}}
    <div class="flex items-center gap-2">
        <input
            type="text"
            placeholder="New deck name..."
            wire:model="newName"
            class="border rounded px-3 py-2 w-full focus:outline-none focus:ring focus:ring-gray-200"
        >
        <button wire:click="createDeck" class="px-4 py-2 rounded bg-black text-white hover:opacity-90">
            Create
        </button>
    </div>

    {{-- Deck list --}}
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @forelse ($decks as $deck)
            <div class="group border rounded-xl p-4 hover:shadow-sm transition bg-white">
                {{-- Header: time + ID badge --}}
                <div class="flex items-center justify-between">
                    <div class="text-xs text-gray-500">{{ $deck->created_at?->diffForHumans() }}</div>
                    <div class="text-[11px] px-2 py-1 rounded border text-gray-600 bg-gray-50">
                        #{{ $deck->id }}
                    </div>
                </div>

                {{-- Title --}}
                <div class="mt-2 text-lg font-semibold line-clamp-2">
                    <a href="{{ route('decks.show', $deck) }}"
                       class="hover:underline decoration-2">
                        {{ $deck->name }}
                    </a>
                </div>

                {{-- Description (optional) --}}
                @if(!empty($deck->description))
                    <div class="mt-2 text-sm text-gray-600 line-clamp-2">
                        {{ $deck->description }}
                    </div>
                @endif

                {{-- Footer actions --}}
                <div class="mt-4 flex items-center justify-between">
                    {{-- (tuỳ chọn) due count nhanh --}}
                    <span class="text-xs text-gray-500">
                        {{-- nếu muốn hiển thị số due, bạn có thể eager load riêng; tạm để placeholder hoặc bỏ đi --}}
                    </span>

                    <div class="flex items-center gap-2">
                        <a href="{{ route('decks.show', $deck) }}"
                           class="px-3 py-1.5 rounded border text-sm hover:bg-gray-50">
                            Open
                        </a>
                        <a href="{{ route('study.panel', $deck) }}"
                           class="px-3 py-1.5 rounded bg-black text-white text-sm hover:opacity-90">
                            Study
                        </a>
                        <a href="{{ route('decks.analytics', $deck) }}"
                           class="px-3 py-1.5 rounded border text-sm hover:bg-gray-50">
                            Analytics
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full text-gray-500">No decks found.</div>
        @endforelse
    </div>

    {{-- Pagination --}}
    <div>
        {{ $decks->links() }}
    </div>
</div>
