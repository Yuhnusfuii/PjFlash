{{-- resources/views/livewire/study/study-all-panel.blade.php --}}
<div class="max-w-3xl p-6 mx-auto space-y-6">

    @php
        $progress = min(100, (int) round(($reviewsThisSession / max(1,$maxReviewsPerSession)) * 100));
    @endphp

    <div class="space-y-2">
        <div class="flex flex-wrap items-center gap-3 text-sm text-gray-600 dark:text-slate-300">
            <div><span class="font-medium">All decks</span></div>
            <span class="hidden sm:inline">•</span>
            <div>Reviews: <span class="font-semibold">{{ $reviewsThisSession }}</span>/<span>{{ $maxReviewsPerSession }}</span></div>
            <span class="hidden sm:inline">•</span>
            <div>New today: <span class="font-semibold">{{ $newThisSession }}</span>/<span>{{ $maxNewPerSession }}</span></div>
            <span class="hidden sm:inline">•</span>
            <div>Due remaining: <span class="font-semibold">{{ $dueRemaining }}</span></div>
            <span class="hidden sm:inline">•</span>
            <div>New remaining: <span class="font-semibold">{{ $newRemaining }}</span></div>
        </div>

        <div class="w-full h-2 overflow-hidden bg-gray-100 dark:bg-slate-800 rounded-full">
            <div class="h-2 bg-gray-900 dark:bg-white" style="width: {{ $progress }}%"></div>
        </div>

        <div class="flex items-center gap-2 text-xs">
            @foreach($presets as $p)
                <button
                    wire:click="setPreset({{ $p }})"
                    title="Giới hạn phiên = {{ $p }}"
                    @class([
                        'px-2 py-1 rounded-full border transition',
                        $maxReviewsPerSession === $p
                            ? 'bg-emerald-600 border-emerald-600 text-white'
                            : 'bg-gray-50 dark:bg-slate-900 border-gray-200 dark:border-slate-700 text-gray-700 dark:text-slate-300 hover:bg-gray-100 dark:hover:bg-slate-800',
                    ])
                >
                    💡 {{ $p }}
                </button>
            @endforeach
        </div>
    </div>

    <div class="flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-2 text-sm">
            <span class="text-gray-500 dark:text-slate-400">Queue:</span>
            <div class="flex gap-1">
                <button wire:click="setQueueMode('due')"
                        @class(['px-3 py-1.5 rounded-xl border transition',
                            $queueMode === 'due' ? 'bg-gray-900 text-white border-gray-900' : 'bg-white dark:bg-slate-900 text-gray-700 dark:text-slate-300 border-gray-200 dark:border-slate-700 hover:bg-gray-50 dark:hover:bg-slate-800'])>
                    Only due
                </button>
                <button wire:click="setQueueMode('mix')"
                        @class(['px-3 py-1.5 rounded-xl border transition',
                            $queueMode === 'mix' ? 'bg-gray-900 text-white border-gray-900' : 'bg-white dark:bg-slate-900 text-gray-700 dark:text-slate-300 border-gray-200 dark:border-slate-700 hover:bg-gray-50 dark:hover:bg-slate-800'])>
                    Mix
                </button>
                <button wire:click="setQueueMode('new')"
                        @class(['px-3 py-1.5 rounded-xl border transition',
                            $queueMode === 'new' ? 'bg-gray-900 text-white border-gray-900' : 'bg-white dark:bg-slate-900 text-gray-700 dark:text-slate-300 border-gray-200 dark:border-slate-700 hover:bg-gray-50 dark:hover:bg-slate-800'])>
                    Only new
                </button>
            </div>
        </div>
    </div>

    @if ($sessionEnded)
        <div class="p-10 text-center bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm rounded-2xl">
            <h3 class="text-lg font-semibold">Session complete 🎉</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-slate-400">
                Không còn thẻ theo chế độ <strong>{{ strtoupper($queueMode) }}</strong>
                hoặc bạn đã đạt giới hạn phiên.
            </p>
            <div class="flex items-center justify-center gap-2 mt-4">
                <a href="{{ route('dashboard') }}" class="px-4 py-2 border rounded-lg hover:bg-gray-50 dark:hover:bg-slate-800">Về Dashboard</a>
                <button wire:click="$refresh" class="px-4 py-2 text-white bg-black rounded-lg hover:opacity-90">Refresh</button>
            </div>
        </div>
    @else
        <div class="flex items-center justify-between text-xs text-gray-500 dark:text-slate-400">
            <div>Queue: <span class="font-medium uppercase">{{ $queueMode }}</span></div>
            <div>ID: {{ $current?->id }} @if($current && $current->deck) • Deck: {{ $current->deck->name }} @endif</div>
        </div>

        @if (!$current)
            <div class="p-10 text-center bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm rounded-2xl">
                <h3 class="text-lg font-semibold">Không có thẻ để học.</h3>
            </div>
        @else
            {{-- Dùng cùng partial flashcard --}}
            @include('study.partials.flashcard', ['item' => $current, 'showAnswer' => $showAnswer])
        @endif
    @endif
</div>
