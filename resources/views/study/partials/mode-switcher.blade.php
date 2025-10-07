{{-- resources/views/study/partials/mode-switcher.blade.php --}}
{{-- NOTE: Tab chọn chế độ học. Yêu cầu Livewire component có method setMode($mode). --}}

@php
    $modes = [
        'auto'      => 'Auto',
        'flashcard' => 'Flashcard',
        'mcq'       => 'MCQ',
        'matching'  => 'Matching',
    ];
@endphp

<div class="flex flex-wrap gap-2">
    @foreach($modes as $key => $label)
        <button
            wire:click="setMode('{{ $key }}')"
            @class([
                'px-3 py-1.5 rounded-xl border text-sm transition',
                'bg-gray-900 text-white border-gray-900' => $mode === $key,
                'bg-white hover:bg-gray-50' => $mode !== $key,
            ])
            title="Switch to {{ $label }}"
        >
            {{ $label }}
        </button>
    @endforeach
</div>
