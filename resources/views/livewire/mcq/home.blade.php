{{-- resources/views/livewire/mcq/home.blade.php --}}
<div class="max-w-6xl mx-auto space-y-6">

  {{-- Header --}}
  <div class="flex items-center justify-between gap-3">
    <div>
      <h1 class="text-2xl font-semibold">MCQ – Kiểm tra</h1>
      <p class="mt-1 text-slate-500 dark:text-slate-400">Chọn deck, chế độ &amp; số câu rồi bắt đầu.</p>
    </div>
  </div>

  {{-- Controls --}}
  <div class="y-card y-card-pad">
    <div class="grid gap-3 md:grid-cols-3 md:items-end">
      <div class="md:col-span-1">
        <label class="y-label mb-1">Tìm deck</label>
        <input type="text" wire:model.live="q" class="y-input" placeholder="Nhập từ khóa…">
      </div>
      <div>
        <label class="y-label mb-1">Chế độ</label>
        <select wire:model="mode" class="y-select">
          <option value="mixed">Mixed</option>
          <option value="front_to_back">Front → Back</option>
          <option value="back_to_front">Back → Front</option>
        </select>
      </div>
      <div class="flex items-end gap-2">
        <div class="flex-1">
          <label class="y-label mb-1">Số câu</label>
          <input type="number" min="5" max="50" step="1" wire:model="num" class="y-input">
        </div>
      </div>
    </div>
  </div>

  {{-- Deck list --}}
  @if($decks->isEmpty())
    <div class="y-card y-card-pad text-slate-500 dark:text-slate-400">
      Không tìm thấy deck nào.
    </div>
  @else
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
      @foreach ($decks as $deck)
        @php
          $itemsCount = (int) ($deck->items_count ?? 0);
          $enough = $itemsCount >= 4;
        @endphp

        <div class="y-card y-card-pad">
          <div class="flex items-start justify-between gap-3">
            <div>
              <h3 class="font-semibold">{{ $deck->name }}</h3>
              <div class="mt-0.5 text-sm text-slate-500 dark:text-slate-400">{{ $itemsCount }} items</div>
            </div>

            {{-- radio (optional) --}}
            <label class="inline-flex items-center gap-2">
              <input type="radio"
                     name="deck_pick"
                     wire:model="deckId"
                     value="{{ $deck->id }}"
                     class="h-4 w-4">
            </label>
          </div>

          @unless($enough)
            <div class="mt-3 text-xs text-rose-600 dark:text-rose-400">
              ⚠️ Need at least 4 cards to generate MCQ.
            </div>
          @endunless

          <div class="mt-4">
            {{-- ✅ gọi thẳng startDeck(id) để không phụ thuộc state radio --}}
            <button
              type="button"
              class="y-btn {{ $enough ? 'y-btn--brand' : '' }} w-full justify-center {{ $enough ? '' : 'cursor-not-allowed opacity-60' }}"
              @if($enough) wire:click="startDeck({{ $deck->id }})" @else disabled @endif>
              Start quiz
            </button>
          </div>
        </div>
      @endforeach
    </div>

    <div class="mt-4">
      {{ $decks->links() }}
    </div>
  @endif
</div>
