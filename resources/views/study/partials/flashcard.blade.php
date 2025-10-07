{{-- resources/views/study/partials/flashcard.blade.php --}}
{{-- NOTE: Hiển thị Flashcard với nút Show/Hide Answer và grade 0..3. --}}

@php
    $front = trim((string)($item->front ?? ''));
    $back  = trim((string)($item->back ?? ''));
    $data  = $item->data ?? [];
@endphp

<div class="p-6 space-y-4 bg-white border shadow-sm rounded-2xl">
    {{-- Câu hỏi --}}
    <div>
        <div class="mb-2 text-xs text-gray-400 uppercase">Question</div>
        @if($front !== '')
            <div class="prose max-w-none">{{ $front }}</div>
        @elseif(!empty($data))
            <pre class="p-3 overflow-x-auto text-xs rounded-lg bg-gray-50">{{ json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
        @else
            <div class="text-sm text-gray-400">Không có nội dung mặt trước.</div>
        @endif
    </div>

    {{-- Answer / nút Show Answer --}}
    @if(!$showAnswer)
        <div>
            <button
                wire:click="$set('showAnswer', true)"
                class="px-4 py-2 text-white bg-black rounded-lg hover:opacity-90"
            >
                Show Answer
            </button>
        </div>
    @else
        <div class="p-4 border rounded-xl bg-emerald-50/40">
            <div class="mb-2 text-xs text-gray-500 uppercase">Answer</div>
            @if($back !== '')
                <div class="prose max-w-none">{{ $back }}</div>
            @elseif(!empty($data))
                <pre class="p-3 overflow-x-auto text-xs bg-white rounded-lg">{{ json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
            @else
                <div class="text-sm text-gray-400">Không có nội dung mặt sau.</div>
            @endif
        </div>

        {{-- Grade buttons (Again / Hard / Good / Easy) --}}
        <div class="flex flex-wrap items-center gap-2">
            <button wire:click="grade(0)" class="px-3 py-2 rounded-xl bg-red-50 hover:bg-red-100">Again</button>
            <button wire:click="grade(1)" class="px-3 py-2 rounded-xl bg-amber-50 hover:bg-amber-100">Hard</button>
            <button wire:click="grade(2)" class="px-3 py-2 rounded-xl bg-emerald-50 hover:bg-emerald-100">Good</button>
            <button wire:click="grade(3)" class="px-3 py-2 rounded-xl bg-blue-50 hover:bg-blue-100">Easy</button>

            <button
                wire:click="$set('showAnswer', false)"
                class="px-3 py-2 ml-auto text-xs border rounded-lg hover:bg-gray-50"
                title="Ẩn đáp án"
            >
                Hide
            </button>
        </div>
    @endif
</div>
