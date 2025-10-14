<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold">MCQ – {{ $title }}</h1>
            <div class="text-sm text-slate-500">Mode: {{ $this->mode }} • {{ $progress }}</div>
        </div>
        <a href="{{ route('mcq.home') }}" class="btn-outline">Back</a>
    </div>

    @if (empty($this->questions))
        <div class="card p-4 text-yellow-800 bg-yellow-50 rounded-xl">
            Not enough data to build Global MCQ. Please add more cards to your decks (each deck needs ≥ 4 items).
        </div>
    @elseif ($this->finished)
        <div class="card p-6 text-center space-y-3">
            <div class="text-3xl font-bold">Kết quả: {{ $score }}/{{ $total }}</div>
            <div class="text-slate-500">Làm lại để ôn thêm nhé!</div>
            <div class="flex items-center justify-center gap-3">
                <button wire:click="retry" class="btn">Làm lại</button>
                <a href="{{ route('mcq.home') }}" class="btn-outline">Chọn chế độ khác</a>
            </div>
        </div>
    @else
        @if ($q)
            <div class="card p-6 space-y-4">
                <div class="flex flex-wrap items-center gap-2 text-sm text-slate-500">
                    <span>Câu {{ $this->i + 1 }} / {{ $total }}</span>
                    <span class="mx-1">•</span>
                    <span>Hướng: {{ $q['direction'] === 'front_to_back' ? 'Front → Back' : 'Back → Front' }}</span>
                    <span class="mx-1">•</span>
                    <span>Deck: {{ $q['deck_name'] }}</span>
                </div>

                <div class="text-xl font-semibold">{{ $q['prompt'] }}</div>

                <div class="grid md:grid-cols-2 gap-3">
                    @foreach ($q['options'] as $idx => $opt)
                        @php
                            $isPicked  = $picked === $idx;
                            $isCorrect = $q['correctIndex'] === $idx;

                            $classes = 'w-full text-left border rounded-xl px-4 py-3 transition hover:bg-slate-50';
                            if ($picked !== null) {
                                $classes .= $isCorrect
                                    ? ' border-emerald-500 bg-emerald-50'
                                    : ($isPicked ? ' border-rose-500 bg-rose-50' : '');
                            } elseif ($isPicked) {
                                $classes .= ' border-sky-500';
                            }
                        @endphp

                        <button class="{{ $classes }}" wire:click="choose({{ $idx }})">
                            <div class="font-medium">{{ $opt }}</div>
                        </button>
                    @endforeach
                </div>

                <div class="flex items-center justify-between">
                    <div class="text-sm text-slate-500">{{ $progress }}</div>
                    <button class="btn" wire:click="next" @disabled($picked === null)>
                        {{ $this->i + 1 < $total ? 'Next' : 'Finish' }}
                    </button>
                </div>
            </div>
        @endif
    @endif
</div>
