{{-- resources/views/livewire/decks/deck-show.blade.php --}}

<div class="space-y-6">

    {{-- HEADER --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold">{{ $deck->name }}</h1>
            @if($deck->description)
                <p class="text-slate-500 dark:text-slate-400 mt-1">{{ $deck->description }}</p>
            @endif
        </div>

        <div class="flex gap-2">
            <a href="{{ route('decks.study', $deck) }}" class="btn btn-outline">Study</a>
            <a href="{{ route('decks.analytics', $deck) }}" class="btn btn-outline">Analytics</a>
            <a href="{{ route('decks.edit', $deck) }}" class="btn btn-outline">Edit Deck</a>
            <a href="{{ route('flashcards.create', $deck->id) }}" class="btn btn-success">+ New Flashcard</a>
            <button type="button" class="btn btn-danger"
                    wire:click="deleteDeck"
                    onclick="event.stopPropagation()">
                Delete Deck
            </button>
        </div>
    </div>

    {{-- INFO LINE --}}
    <div class="y-card p-4 text-sm text-slate-600 dark:text-slate-300 flex items-center gap-3">
        <div><span class="font-semibold">{{ $items->total() }}</span> items</div>
        <div class="hidden md:block h-4 w-px bg-slate-200 dark:bg-slate-700"></div>
        <div class="flex-1">Click vào thẻ để lật • Nhấn <kbd class="kbd">Enter</kbd> / <kbd class="kbd">Space</kbd> cũng được.</div>
    </div>

    {{-- GRID OF CARDS --}}
    @if($items->count() === 0)
        <div class="y-card p-6 text-slate-500 dark:text-slate-400">
            Chưa có flashcard nào. Hãy thêm thẻ với nút <strong>+ New Flashcard</strong>.
        </div>
    @else
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
        @foreach ($items as $item)
        <div class="fc y-card p-0" tabindex="0">
            <div class="fc-inner">

                {{-- FRONT --}}
                <div class="fc-face front">
                    <span class="fc-badge">Front</span>

                    <div class="fc-title">{{ $item->front }}</div>

                    <div class="fc-actions">
                        <span>Click để lật • Enter / Space</span>
                        <div class="ml-auto flex gap-2 fc-actions">
                            <a href="{{ route('flashcards.edit', [$deck->id, $item->id]) }}"
                               class="btn btn-outline"
                               onclick="event.stopPropagation()">Edit</a>
                            <button type="button"
                                    class="btn btn-danger"
                                    onclick="event.stopPropagation(); @this.call('confirmDelete', {{ $item->id }})">
                                Delete
                            </button>
                            <button type="button" class="btn btn-success" onclick="flipCard(this)">Flip</button>
                        </div>
                    </div>
                </div>

                {{-- BACK --}}
                <div class="fc-face back">
                    <span class="fc-badge fc-back">Back</span>

                    <div class="fc-body">{!! nl2br(e($item->back)) !!}</div>

                    <div class="fc-actions">
                        <span>Click để lật lại • Enter / Space</span>
                        <div class="ml-auto flex gap-2 fc-actions">
                            <a href="{{ route('flashcards.edit', [$deck->id, $item->id]) }}"
                               class="btn btn-outline"
                               onclick="event.stopPropagation()">Edit</a>
                            <button type="button"
                                    class="btn btn-danger"
                                    onclick="event.stopPropagation(); @this.call('confirmDelete', {{ $item->id }})">
                                Delete
                            </button>
                            <button type="button" class="btn btn-success" onclick="flipCard(this)">Flip</button>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        @endforeach
    </div>

    {{-- PAGINATION --}}
    <div class="mt-4">
        {{ $items->links() }}
    </div>
    @endif
</div>

@push('head')
<style>
  /* ===== Utilities ===== */
  :root{ --card-bg:#fff; --card-br:#e2e8f0 }
  .dark :root, .dark{ --card-bg:#0f172a; --card-br:#334155 }

  .y-card{ border-radius:18px; background:var(--card-bg); border:1px solid var(--card-br); }

  .btn{display:inline-flex;align-items:center;justify-content:center;padding:.5rem .9rem;border-radius:9999px;font-weight:600}
  .btn-outline{border:1px solid #cbd5e1}
  .btn-danger{background:#ef4444;color:#fff}
  .btn-success{background:#10b981;color:#fff}

  .kbd{padding:.15rem .35rem;border-radius:.375rem;border:1px solid #cbd5e1;background:#f8fafc}
  .dark .kbd{border-color:#334155;background:#0b1220}

  /* ===== FLIP CARD ===== */
  .fc { perspective: 1200px; outline: none; }
  .fc-inner{
    position: relative; width: 100%; height: 100%;
    transform-style: preserve-3d;
    transition: transform .5s cubic-bezier(.2,.7,.2,1);
    will-change: transform;
  }
  .fc.is-flipped .fc-inner{ transform: rotateY(180deg); }

  .fc-face{
    position:absolute; inset:0;
    backface-visibility: hidden;
    border-radius: 16px;
    border: 1px solid var(--card-br);
    background: var(--card-bg);
    padding: 16px;
    display:flex; flex-direction:column;
    gap:.75rem;
    min-height: 190px;
    user-select: none;
  }
  .fc-face.back{ transform: rotateY(180deg); }

  @media(hover:hover){
    .fc:not(.is-flipped) .fc-face.front:hover{
      box-shadow: 0 6px 22px -6px rgba(2,6,23,.22);
      cursor: pointer;
    }
  }

  .fc-badge{
    align-self: flex-start;
    font-size:.72rem; line-height:1;
    padding:.35rem .55rem;
    border-radius: 9999px;
    border:1px solid #bae6fd;
    background:#e7f5ff;
    color:#0284c7;
  }
  .dark .fc-badge{
    background:#071927; border-color:#1f3b52; color:#7dd3fc;
  }
  .fc-badge.fc-back{
    border-color:#bbf7d0; background:#ecfdf5; color:#059669;
  }
  .dark .fc-badge.fc-back{
    background:#062016; border-color:#14532d; color:#86efac;
  }

  .fc-title{ font-weight:700; font-size:1.05rem; }
  .fc-body{ font-size:.975rem; color:#0f172a }
  .dark .fc-body{ color:#e2e8f0 }

  .fc-face > *{ pointer-events:none; }           /* tránh click text làm lật nhầm */
  .fc-actions, .fc-actions *{ pointer-events:auto; }

  .fc-actions{
    margin-top:auto;
    display:flex; align-items:center; gap:.5rem;
    font-size:.84rem; color:#64748b;
  }
  .dark .fc-actions{ color:#94a3b8 }

  .fc:focus-visible { box-shadow: 0 0 0 3px rgba(59,130,246,.35); border-radius:16px; }
</style>
@endpush

@push('scripts')
<script>
  // Flip khi click vào card (ngoài vùng nút)
  document.addEventListener('click', (e) => {
    const card = e.target.closest('.fc');
    if (!card) return;
    if (e.target.closest('.fc-actions')) return; // click vào nút => bỏ
    card.classList.toggle('is-flipped');
  });

  // Flip khi Enter / Space (khi card đang focus)
  document.addEventListener('keydown', (e) => {
    if (e.key !== 'Enter' && e.key !== ' ' && e.code !== 'Space') return;
    const card = document.activeElement?.closest?.('.fc');
    if (!card) return;
    e.preventDefault();
    card.classList.toggle('is-flipped');
  });

  // Flip bằng nút "Flip"
  window.flipCard = (btn) => {
    const card = btn.closest('.fc');
    if (card) card.classList.toggle('is-flipped');
  };
</script>
@endpush
