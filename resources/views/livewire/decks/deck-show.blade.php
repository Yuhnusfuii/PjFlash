@push('head')
<style>
/* ===== Flip Card (3D) – đẹp, mượt ===== */
.card3d { perspective: 1200px; height: 12rem; }
.card3d-inner {
  position: relative; width: 100%; height: 100%;
  transform-style: preserve-3d; transition: transform .6s cubic-bezier(.2,.7,.2,1);
}
.card3d.is-flipped .card3d-inner { transform: rotateY(180deg); }

.card3d-face {
  position: absolute; inset: 0; backface-visibility: hidden;
  border-radius: 16px; padding: 16px 18px; display: flex; flex-direction: column; gap: 10px;
  border: 1px solid rgba(148,163,184,.35);
  background: linear-gradient(180deg, rgba(255,255,255,.92), rgba(255,255,255,.78));
  box-shadow: 0 8px 20px rgba(2,6,23,.06);
}
.dark .card3d-face {
  background: linear-gradient(180deg, rgba(15,23,42,.92), rgba(15,23,42,.82));
  border-color: rgba(51,65,85,.6);
  box-shadow: 0 8px 24px rgba(0,0,0,.35);
}
.card3d-back { transform: rotateY(180deg); }

.card3d-label { font-size:.70rem; letter-spacing:.04em; color:#64748b; }
.dark .card3d-label { color:#94a3b8; }

.card-title { font-weight: 600; line-height: 1.35; }
.card-answer { color:#047857; }             /* emerald-700 */
.dark .card-answer { color:#34d399; }        /* emerald-400 */

.card3d-cta { opacity:0; transform: translateY(4px); transition: all .25s ease; font-size: .75rem; color:#64748b; }
.card3d:hover .card3d-cta { opacity:1; transform:none; }

.card3d-actions { position:absolute; top:10px; right:10px; display:flex; gap:.4rem; }
</style>
@endpush

<div class="max-w-6xl p-6 mx-auto space-y-6"
     x-data="{
        showItemModal: @entangle('showItemModal').live,
        showDeckModal: @entangle('showDeckModal').live
     }"
     @keydown.escape.window="
        if (showItemModal) showItemModal = false;
        if (showDeckModal) showDeckModal = false;
     ">

    {{-- Flash --}}
    @if (session('success'))
        <div class="p-3 text-green-800 bg-green-100 rounded">
            {{ session('success') }}
        </div>
    @endif

    {{-- Header --}}
    <div class="flex items-start justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold">{{ $deck->name }}</h1>
            @if(!empty($deck->description))
                <p class="mt-1 text-sm text-gray-600">{{ $deck->description }}</p>
            @endif
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('decks.study', ['deck' => $deck->id]) }}" class="px-3 py-2 border rounded hover:bg-gray-50">Study</a>
            <a href="{{ route('decks.analytics', ['deck' => $deck->id]) }}" class="px-3 py-2 border rounded hover:bg-gray-50">Analytics</a>
            <a href="{{ route('decks.edit', ['deck' => $deck->id]) }}" class="px-3 py-2 border rounded hover:bg-gray-50">Edit Deck</a>
            <a href="{{ route('flashcards.create', ['deckId' => $deck->id]) }}" class="px-3 py-2 text-white bg-blue-600 rounded hover:bg-blue-700">+ New Flashcard</a>
            <button wire:click="confirmDeleteDeck" class="px-3 py-2 text-white bg-red-600 rounded hover:bg-red-700">Delete Deck</button>
        </div>
    </div>

    <div class="text-sm text-gray-600">
        Showing {{ $items->firstItem() ?? 0 }}–{{ $items->lastItem() ?? 0 }} of {{ $items->total() }}
    </div>

    {{-- GRID + FLIP --}}
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
        @forelse ($items as $it)
            <div class="card3d group"
                 x-data="{ flipped:false }"
                 :class="flipped ? 'is-flipped' : ''"
                 role="button" tabindex="0"
                 @click="flipped = !flipped"
                 @keydown.enter.prevent="flipped = !flipped"
                 @keydown.space.prevent="flipped = !flipped">

                <div class="card3d-inner">
                    {{-- FRONT --}}
                    <div class="card3d-face">
                        <div class="card3d-actions" @click.stop>
                            <a href="{{ route('flashcards.edit', ['deckId' => $deck->id, 'itemId' => $it->id]) }}"
                               class="px-2 py-1 text-xs border rounded bg-white/70 dark:bg-slate-900/60 hover:bg-white dark:hover:bg-slate-800">Edit</a>
                            <button class="px-2 py-1 text-xs text-white bg-red-600 rounded hover:bg-red-700"
                                    wire:click="confirmDeleteItem({{ $it->id }})">Delete</button>
                        </div>

                        <span class="card3d-label">Front</span>
                        <div class="card-title">
                            {{ $it->front ?? data_get($it, 'data.front') ?? '—' }}
                        </div>

                        <div class="mt-auto card3d-cta">Click to flip</div>
                    </div>

                    {{-- BACK --}}
                    <div class="card3d-face card3d-back">
                        <div class="card3d-actions" @click.stop>
                            <a href="{{ route('flashcards.edit', ['deckId' => $deck->id, 'itemId' => $it->id]) }}"
                               class="px-2 py-1 text-xs border rounded bg-white/70 dark:bg-slate-900/60 hover:bg-white dark:hover:bg-slate-800">Edit</a>
                            <button class="px-2 py-1 text-xs text-white bg-red-600 rounded hover:bg-red-700"
                                    wire:click="confirmDeleteItem({{ $it->id }})">Delete</button>
                        </div>

                        <span class="card3d-label">Back</span>
                        <div class="card-title card-answer">
                            {{ $it->back ?? data_get($it, 'data.back') ?? '—' }}
                        </div>

                        <div class="mt-auto card3d-cta">Click to flip back</div>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-gray-600">No flashcards yet. Create the first one!</div>
        @endforelse
    </div>

    @if($items->hasPages())
        <div class="pt-2">
            {{ $items->links() }}
        </div>
    @endif

    {{-- MODAL: Delete Item --}}
    <div x-show="showItemModal" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center"
         x-transition.opacity>
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="showItemModal=false"></div>
        <div class="relative w-full max-w-sm p-6 bg-white shadow-xl dark:bg-slate-900 rounded-2xl ring-1 ring-gray-200 dark:ring-slate-700"
             x-transition.scale.origin.center>
            <div class="flex items-start gap-3">
                <div class="flex items-center justify-center w-10 h-10 bg-red-100 rounded-full shrink-0">🗑️</div>
                <div>
                    <h2 class="text-lg font-semibold">Delete flashcard?</h2>
                    <p class="mt-1 text-gray-600">This action cannot be undone.</p>
                </div>
            </div>
            <div class="flex justify-end gap-2 mt-6">
                <button @click="showItemModal=false" class="px-4 py-2 border rounded-lg hover:bg-gray-50 dark:hover:bg-slate-800">Cancel</button>
                <button wire:click="deleteItem" class="px-4 py-2 text-white bg-red-600 rounded-lg hover:bg-red-700">Delete</button>
            </div>
        </div>
    </div>

    {{-- MODAL: Delete Deck --}}
    <div x-show="showDeckModal" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center"
         x-transition.opacity>
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="showDeckModal=false"></div>
        <div class="relative w-full max-w-sm p-6 bg-white shadow-xl dark:bg-slate-900 rounded-2xl ring-1 ring-gray-200 dark:ring-slate-700"
             x-transition.scale.origin.center>
            <div class="flex items-start gap-3">
                <div class="flex items-center justify-center w-10 h-10 bg-red-100 rounded-full shrink-0">⚠️</div>
                <div>
                    <h2 class="text-lg font-semibold">Delete deck?</h2>
                    <p class="mt-1 text-gray-600">This will permanently delete the deck and its flashcards.</p>
                </div>
            </div>
            <div class="flex justify-end gap-2 mt-6">
                <button @click="showDeckModal=false" class="px-4 py-2 border rounded-lg hover:bg-gray-50 dark:hover:bg-slate-800">Cancel</button>
                <button wire:click="deleteDeck" class="px-4 py-2 text-white bg-red-600 rounded-lg hover:bg-red-700">Delete</button>
            </div>
        </div>
    </div>
</div>
