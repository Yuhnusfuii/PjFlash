{{-- resources/views/study/partials/mcq.blade.php --}}
@php
    $mcq         = $item->data['mcq'] ?? null;
    $options     = is_array($mcq) ? ($mcq['options'] ?? []) : [];
    $hasPayload  = is_array($options) && count($options) >= 2;
    $answerIndex = (int)($mcq['answer_index'] ?? 0);
@endphp

<div class="p-6 space-y-4 bg-white border shadow-sm rounded-2xl">
    <div class="text-xs text-gray-400 uppercase">Mode: MCQ</div>
    <h3 class="text-lg font-semibold">Q: {{ $mcq['question'] ?? ($item->front ?? '—') }}</h3>

    @if(!$hasPayload)
        <p class="text-sm text-gray-500">No MCQ data for this item yet.</p>
        <button
            class="px-4 py-2 text-white bg-blue-600 rounded hover:opacity-90"
            x-data
            x-on:click="
                await fetch('/sanctum/csrf-cookie', { credentials: 'same-origin' });
                const res = await fetch('{{ route('api.items.mcq.generate', $item) }}', {
                    method: 'POST',
                    headers: { 'Accept':'application/json', 'X-CSRF-TOKEN':'{{ csrf_token() }}' },
                    credentials: 'same-origin'
                });
                if (res.ok) { $wire.refreshCurrent?.(); }
            "
        >Generate MCQ</button>
    @else
        <div class="grid grid-cols-1 gap-2"
             x-data="{ picked: null, submitted: false, correct: {{ $answerIndex }} }"
             x-on:keydown.window="
                if (/^[1-9]$/.test($event.key)) picked = parseInt($event.key) - 1;
                if ($event.key === 'Enter' && picked !== null && !submitted) { submitted = true; $wire.grade(picked === correct ? 5 : 1); }
             ">
            @foreach($options as $idx => $opt)
                <label class="flex items-center gap-2 p-3 border rounded cursor-pointer"
                       :class="submitted
                                ? ({{ $idx }} === correct
                                    ? 'bg-green-50 border-green-300'
                                    : (picked === {{ $idx }} ? 'bg-red-50 border-red-300' : ''))
                                : (picked === {{ $idx }} ? 'bg-gray-50 border-gray-300' : '')">
                    <input type="radio" class="w-4 h-4" name="mcq_option" :disabled="submitted"
                           @click="picked = {{ $idx }}">
                    <span>{{ $opt }}</span>
                </label>
            @endforeach

            <div class="flex items-center gap-2 mt-2">
                <button class="px-4 py-2 text-white bg-black rounded hover:opacity-90 disabled:opacity-50"
                        :disabled="picked === null || submitted"
                        @click="submitted = true; $wire.grade(picked === correct ? 5 : 1);">
                    Submit
                </button>

                <template x-if="submitted">
                    <p class="text-sm" :class="picked === correct ? 'text-green-700' : 'text-red-700'">
                        <template x-if="picked === correct">✅ Correct!</template>
                        <template x-if="picked !== correct">❌ Incorrect.</template>
                        &nbsp;Answer: <strong>{{ $options[$answerIndex] ?? '' }}</strong>
                    </p>
                </template>
            </div>
        </div>
    @endif
</div>
