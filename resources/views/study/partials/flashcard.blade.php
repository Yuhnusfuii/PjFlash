{{-- resources/views/study/partials/flashcard.blade.php --}}
@php
    $front = trim((string)($item->front ?? ''));
    $back  = trim((string)($item->back ?? ''));
    $data  = $item->data ?? [];
@endphp

<div class="y-card y-card-pad space-y-4">
    {{-- Question --}}
    <div>
        <div class="mb-2 text-xs tracking-wide text-slate-400 dark:text-slate-500 uppercase">Question</div>
        @if($front !== '')
            <div class="prose max-w-none">{{ $front }}</div>
        @elseif(!empty($data))
            <pre class="p-3 overflow-x-auto text-xs rounded-lg bg-slate-50 dark:bg-slate-800 text-slate-700 dark:text-slate-200">{{ json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
        @else
            <div class="text-sm text-slate-400">Không có nội dung mặt trước.</div>
        @endif
    </div>

    {{-- Answer / Show --}}
    @if(!$showAnswer)
        <div>
            <button wire:click="$set('showAnswer', true)" class="btn btn-success">Show Answer</button>
        </div>
    @else
        <div class="p-4 rounded-xl border border-emerald-200/70 dark:border-emerald-800 bg-emerald-50/50 dark:bg-emerald-900/20">
            <div class="mb-2 text-xs tracking-wide text-emerald-700 dark:text-emerald-300 uppercase">Answer</div>
            @if($back !== '')
                <div class="prose max-w-none">{{ $back }}</div>
            @elseif(!empty($data))
                <pre class="p-3 overflow-x-auto text-xs rounded-lg bg-white dark:bg-slate-900">{{ json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
            @else
                <div class="text-sm text-slate-400">Không có nội dung mặt sau.</div>
            @endif
        </div>

        {{-- Grade buttons --}}
        <div class="flex flex-wrap items-center gap-2">
            <button wire:click="grade(0)" class="px-3 py-2 rounded-2xl border border-red-200 dark:border-red-900 bg-red-50 dark:bg-red-900/20 hover:bg-red-100 dark:hover:bg-red-900/30 text-red-700 dark:text-red-300">Again</button>
            <button wire:click="grade(1)" class="px-3 py-2 rounded-2xl border border-amber-200 dark:border-amber-900 bg-amber-50 dark:bg-amber-900/20 hover:bg-amber-100 dark:hover:bg-amber-900/30 text-amber-700 dark:text-amber-300">Hard</button>
            <button wire:click="grade(2)" class="px-3 py-2 rounded-2xl border border-emerald-200 dark:border-emerald-900 bg-emerald-50 dark:bg-emerald-900/20 hover:bg-emerald-100 dark:hover:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300">Good</button>
            <button wire:click="grade(3)" class="px-3 py-2 rounded-2xl border border-sky-200 dark:border-sky-900 bg-sky-50 dark:bg-sky-900/20 hover:bg-sky-100 dark:hover:bg-sky-900/30 text-sky-700 dark:text-sky-300">Easy</button>

            <button wire:click="$set('showAnswer', false)"
                    class="ml-auto px-3 py-2 rounded-2xl border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800 text-xs">
                Hide
            </button>
        </div>
    @endif
</div>

@push('head')
<style>
/* dùng cùng tokens của layout (app.blade) */
.y-card{ border-radius:16px; background:var(--card-bg); border:1px solid var(--card-br); }
.y-card-pad{ padding:1rem; } @media(min-width:768px){ .y-card-pad{ padding:1.25rem; } }

.btn{display:inline-flex;align-items:center;justify-content:center;padding:.5rem .9rem;border-radius:9999px;font-weight:600}
.btn-success{background:#10b981;color:#fff}
</style>
@endpush
