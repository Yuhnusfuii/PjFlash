{{-- resources/views/livewire/study/study-panel.blade.php --}}
<div class="max-w-3xl p-6 mx-auto space-y-6">

    {{-- ===== Session status bar + Progress ===== --}}
    @php
        $progress = min(100, (int) round(($reviewsThisSession / max(1,$maxReviewsPerSession)) * 100));
        $milestones = [10, 50, 100];
    @endphp

    <div class="space-y-2">
        <div class="flex flex-wrap items-center gap-3 text-sm text-gray-600">
            <div><span class="font-medium">{{ $deck->name }}</span></div>
            <span class="hidden sm:inline">•</span>
            <div>Reviews: <span class="font-semibold">{{ $reviewsThisSession }}</span>/<span>{{ $maxReviewsPerSession }}</span></div>
            <span class="hidden sm:inline">•</span>
            <div>New today: <span class="font-semibold">{{ $newThisSession }}</span>/<span>{{ $maxNewPerSession }}</span></div>
            <span class="hidden sm:inline">•</span>
            <div>Due remaining: <span class="font-semibold">{{ $dueRemaining }}</span></div>
            <span class="hidden sm:inline">•</span>
            <div>New remaining: <span class="font-semibold">{{ $newRemaining }}</span></div>
        </div>

        <div class="w-full h-2 overflow-hidden bg-gray-100 rounded-full">
            <div class="h-2 bg-gray-900" style="width: {{ $progress }}%"></div>
        </div>

        <div class="flex items-center gap-2 text-xs">
            @foreach($milestones as $m)
                <span
                    @class([
                        'px-2 py-1 rounded-full border',
                        'bg-emerald-50 border-emerald-300 text-emerald-700' => $reviewsThisSession >= $m,
                        'bg-gray-50 border-gray-200 text-gray-500' => $reviewsThisSession < $m,
                    ])
                    title="Milestone {{ $m }} reviews"
                >
                    🎯 {{ $m }}
                </span>
            @endforeach
        </div>
    </div>

    {{-- ===== Top controls ===== --}}
    <div class="flex flex-wrap items-center justify-between gap-3">
        {{-- Mode Switcher (Study chỉ Flashcard; giữ Auto để không vỡ UI cũ) --}}
        <div class="flex items-center gap-2 text-sm">
            <span class="text-gray-500">Mode:</span>
            <div class="flex gap-1">
                <button wire:click="setMode('auto')"
                        @class(['px-3 py-1.5 rounded-xl border', $mode === 'auto' ? 'bg-gray-900 text-white border-gray-900' : 'bg-white hover:bg-gray-50'])>
                    Auto
                </button>
                <button wire:click="setMode('flashcard')"
                        @class(['px-3 py-1.5 rounded-xl border', $mode === 'flashcard' ? 'bg-gray-900 text-white border-gray-900' : 'bg-white hover:bg-gray-50'])>
                    Flashcard
                </button>
            </div>
        </div>

        {{-- Queue Mode Switcher --}}
        <div class="flex items-center gap-2 text-sm">
            <span class="text-gray-500">Queue:</span>
            <div class="flex gap-1">
                <button wire:click="setQueueMode('due')"
                        @class(['px-3 py-1.5 rounded-xl border', $queueMode === 'due' ? 'bg-gray-900 text-white border-gray-900' : 'bg-white hover:bg-gray-50'])>
                    Only due
                </button>
            <button wire:click="setQueueMode('mix')"
                        @class(['px-3 py-1.5 rounded-xl border', $queueMode === 'mix' ? 'bg-gray-900 text-white border-gray-900' : 'bg-white hover:bg-gray-50'])>
                    Mix
                </button>
                <button wire:click="setQueueMode('new')"
                        @class(['px-3 py-1.5 rounded-xl border', $queueMode === 'new' ? 'bg-gray-900 text-white border-gray-900' : 'bg-white hover:bg-gray-50'])>
                    Only new
                </button>
            </div>
        </div>
    </div>

    {{-- ===== End-of-session state ===== --}}
    @if ($sessionEnded)
        <div class="p-10 text-center bg-white border shadow-sm rounded-2xl">
            <h3 class="text-lg font-semibold">Session complete 🎉</h3>
            <p class="mt-1 text-sm text-gray-500">
                Không còn thẻ theo chế độ <strong>{{ strtoupper($queueMode) }}</strong>
                hoặc bạn đã đạt giới hạn phiên.
            </p>
            <div class="flex items-center justify-center gap-2 mt-4">
                <a href="{{ route('decks.show', $deck) }}" class="px-4 py-2 border rounded-lg hover:bg-gray-50">Back to deck</a>
                <button wire:click="$refresh" class="px-4 py-2 text-white bg-black rounded-lg hover:opacity-90">Refresh</button>
                {{-- Lối tắt sang quiz MCQ riêng --}}
                @auth
                    <a href="{{ route('mcq.home') }}" class="px-4 py-2 border rounded-lg hover:bg-gray-50">Go to MCQ Quiz</a>
                @endauth
            </div>
        </div>
    @else
        @php
            // Study chỉ Flashcard; nếu 'auto' thì cũng hiển thị flashcard
            $viewMode = 'flashcard';
        @endphp

        <div class="flex items-center justify-between text-xs text-gray-500">
            <div>Mode: <span class="font-medium uppercase">{{ $viewMode }}</span> • Queue: <span class="font-medium uppercase">{{ $queueMode }}</span></div>
            <div>ID: {{ $current?->id }}</div>
        </div>

        @if (!$current)
            <div class="p-10 text-center bg-white border shadow-sm rounded-2xl">
                <h3 class="text-lg font-semibold">Không có thẻ để học.</h3>
            </div>
        @else
            {{-- Chỉ còn flashcard --}}
            @include('study.partials.flashcard', ['item' => $current, 'showAnswer' => $showAnswer])
        @endif
    @endif
</div>
