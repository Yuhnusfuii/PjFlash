<div class="max-w-6xl mx-auto p-6 space-y-6"
     x-data="{
        showItemModal: @entangle('showItemModal').live,
        showDeckModal: @entangle('showDeckModal').live
     }"
     @keydown.escape.window="
        if (showItemModal) showItemModal = false;
        if (showDeckModal) showDeckModal = false;
     ">

    {{-- Flash message --}}
    @if (session('success'))
        <div class="p-3 rounded bg-green-100 text-green-800">
            {{ session('success') }}
        </div>
    @endif

    {{-- Header --}}
    <div class="flex items-start justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold">{{ $deck->name }}</h1>
            @if(!empty($deck->description))
                <p class="text-sm text-gray-600 mt-1">{{ $deck->description }}</p>
            @endif
        </div>

        {{-- Actions --}}
        <div class="flex flex-wrap items-center gap-2">
            <button wire:click="study" class="px-3 py-2 rounded border hover:bg-gray-50">Study</button>
            <button wire:click="analytics" class="px-3 py-2 rounded border hover:bg-gray-50">Analytics</button>
            <button wire:click="editDeck" class="px-3 py-2 rounded border hover:bg-gray-50">Edit Deck</button>
            <button wire:click="newFlashcard" class="px-3 py-2 rounded bg-blue-600 text-white hover:bg-blue-700">+ New Flashcard</button>

            <button wire:click="confirmDeleteDeck"
                    class="px-3 py-2 rounded bg-red-600 text-white hover:bg-red-700">
                Delete Deck
            </button>
        </div>
    </div>

    {{-- List --}}
    <div class="bg-white border rounded-lg overflow-hidden">
        <div class="border-b p-3 text-sm text-gray-600">
            Showing {{ $items->firstItem() ?? 0 }}–{{ $items->lastItem() ?? 0 }} of {{ $items->total() }}
        </div>

        <div class="divide-y">
            @forelse ($items as $it)
                <div class="p-4 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                    <div class="min-w-0">
                        <div class="font-medium truncate">
                            {{ $it->front ?? data_get($it, 'data.front') ?? '—' }}
                        </div>
                        <div class="text-sm text-gray-600 truncate">
                            {{ $it->back ?? data_get($it, 'data.back') ?? '—' }}
                        </div>
                    </div>

                    <div class="flex items-center gap-2 shrink-0">
                        <a href="{{ route('flashcards.edit', ['deckId' => $deck->id, 'itemId' => $it->id]) }}"
                           class="px-3 py-1.5 rounded border hover:bg-gray-50 text-sm">Edit</a>

                        <button wire:click="confirmDeleteItem({{ $it->id }})"
                                class="px-3 py-1.5 rounded bg-red-600 text-white hover:bg-red-700 text-sm">
                            Delete
                        </button>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-gray-600">No flashcards yet. Create the first one!</div>
            @endforelse
        </div>

        @if($items->hasPages())
            <div class="p-3">
                {{ $items->links() }}
            </div>
        @endif
    </div>

    {{-- Backdrop + MODAL (Delete Item) --}}
    <div x-show="showItemModal" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center"
         x-transition.opacity>
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="showItemModal=false"></div>

        <div class="relative bg-white rounded-2xl shadow-xl max-w-sm w-full p-6 ring-1 ring-gray-200"
             x-transition.scale.origin.center>
            <div class="flex items-start gap-3">
                <div class="shrink-0 w-10 h-10 rounded-full bg-red-100 flex items-center justify-center">🗑️</div>
                <div>
                    <h2 class="text-lg font-semibold">Delete flashcard?</h2>
                    <p class="text-gray-600 mt-1">This action cannot be undone.</p>
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-2">
                <button @click="showItemModal=false"
                        class="px-4 py-2 rounded-lg border hover:bg-gray-50">Cancel</button>
                <button wire:click="deleteItem"
                        class="px-4 py-2 rounded-lg bg-red-600 text-white hover:bg-red-700">Delete</button>
            </div>
        </div>
    </div>

    {{-- Backdrop + MODAL (Delete Deck) --}}
    <div x-show="showDeckModal" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center"
         x-transition.opacity>
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="showDeckModal=false"></div>

        <div class="relative bg-white rounded-2xl shadow-xl max-w-sm w-full p-6 ring-1 ring-gray-200"
             x-transition.scale.origin.center>
            <div class="flex items-start gap-3">
                <div class="shrink-0 w-10 h-10 rounded-full bg-red-100 flex items-center justify-center">⚠️</div>
                <div>
                    <h2 class="text-lg font-semibold">Delete deck?</h2>
                    <p class="text-gray-600 mt-1">This will permanently delete the deck and its flashcards.</p>
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-2">
                <button @click="showDeckModal=false"
                        class="px-4 py-2 rounded-lg border hover:bg-gray-50">Cancel</button>
                <button wire:click="deleteDeck"
                        class="px-4 py-2 rounded-lg bg-red-600 text-white hover:bg-red-700">Delete</button>
            </div>
        </div>
    </div>
</div>
