{{-- resources/views/livewire/study/mcq-panel.blade.php --}}
<div class="max-w-4xl mx-auto space-y-6">

  {{-- Title + Back --}}
  <div class="flex items-center justify-between">
    <h1 class="text-2xl font-semibold">MCQ – {{ $deck->name ?? 'Quiz' }}</h1>
    <a href="{{ route('mcq.home') }}" class="text-sm text-slate-500 hover:underline">Back</a>
  </div>

  {{-- Meta --}}
  <div class="y-card y-card-pad text-sm text-slate-600 dark:text-slate-300">
    <div class="flex flex-wrap items-center gap-2">
      <span>Mode: <span class="font-semibold">{{ $mode }}</span></span>
      <span>•</span>
      <span>{{ ($i ?? 0) + 1 }} / {{ $total }}</span>
      <span>•</span>
      <span>Hướng:
        <span class="font-semibold">
          {{ ($q['direction'] ?? $direction ?? 'back_to_front') === 'front_to_back' ? 'Front → Back' : 'Back → Front' }}
        </span>
      </span>
      <span class="ml-auto text-slate-500">{{ $progress ?? '' }}</span>
    </div>
  </div>

  {{-- Nội dung câu hỏi --}}
  @if (empty($questions ?? []) && empty($q ?? null))
    <div class="y-card y-card-pad text-amber-800 bg-amber-50/60 border border-amber-200 rounded-2xl">
      Not enough cards to build MCQ. Please add more cards to this deck.
    </div>
  @elseif (!empty($finished))
    <div class="y-card y-card-pad text-center space-y-4">
      <div class="text-3xl font-bold">Kết quả: {{ $score }}/{{ $total }}</div>
      <div class="text-slate-500">Làm lại để ôn thêm nhé!</div>
      <div class="flex items-center justify-center gap-3">
        <button wire:click="retry" class="y-btn y-btn--brand">Làm lại</button>
        <a href="{{ route('mcq.home') }}" class="y-btn">Chọn deck khác</a>
      </div>
    </div>
  @else
    @if (!empty($q))
      <div class="y-card y-card-pad space-y-5">
        <div class="flex items-center gap-2 text-sm text-slate-500">
          <span>Câu {{ ($i ?? 0) + 1 }} / {{ $total }}</span>
          <span class="mx-2">•</span>
          <span>Hướng: {{ ($q['direction'] ?? 'back_to_front') === 'front_to_back' ? 'Front → Back' : 'Back → Front' }}</span>
        </div>

        <div class="text-xl font-semibold">{{ $q['prompt'] ?? '' }}</div>

        <div class="grid md:grid-cols-2 gap-3">
          @foreach (($q['options'] ?? []) as $idx => $opt)
            @php
              $isPicked  = isset($picked) && $picked === $idx;
              $isCorrect = isset($q['correctIndex']) && $q['correctIndex'] === $idx;

              $classes = 'y-input text-left h-12 flex items-center rounded-2xl border transition';
              // Hover đẹp như theme:
              $classes .= ' hover:bg-slate-50 dark:hover:bg-slate-800/60';

              if (isset($picked)) {
                  $classes .= $isCorrect
                      ? ' border-emerald-500 bg-emerald-50 dark:bg-emerald-900/20'
                      : ($isPicked ? ' border-rose-500 bg-rose-50 dark:bg-rose-900/20' : '');
              } elseif ($isPicked) {
                  $classes .= ' border-sky-500';
              }
            @endphp

            <button class="{{ $classes }}" wire:click="choose({{ $idx }})">
              <div class="font-medium">{{ $opt }}</div>
            </button>
          @endforeach
        </div>

        <div class="flex items-center justify-between pt-2 text-sm">
          <div class="text-slate-500">{{ $progress ?? '' }}</div>
          <button class="y-btn y-btn--brand"
                  wire:click="next"
                  @disabled(!isset($picked))>
            {{ (($i ?? 0) + 1) < $total ? 'Next' : 'Finish' }}
          </button>
        </div>
      </div>
    @endif
  @endif
</div>
