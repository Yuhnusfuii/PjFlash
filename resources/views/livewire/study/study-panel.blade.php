<div class="max-w-2xl p-6 mx-auto space-y-6 text-center">

    <h1 class="mb-4 text-2xl font-semibold">Study: {{ $deck->name }}</h1>

    @if ($current)
        <div class="p-8 border rounded-lg shadow">
            <div class="text-lg font-medium">Q: {{ $current->front }}</div>

            @if ($showAnswer)
                <div class="mt-4 text-gray-700">
                    <div class="font-medium">A:</div>
                    <div>{{ $current->back }}</div>
                </div>

                <div class="flex flex-wrap justify-center gap-2 mt-6">
                    @foreach([0,1,2,3,4,5] as $g)
                        <button
                            wire:click="grade({{ $g }})"
                            class="px-4 py-2 rounded
                                   {{ $g < 3 ? 'bg-red-500 text-white' : 'bg-green-600 text-white' }}"
                        >
                            {{ $g }}
                        </button>
                    @endforeach
                </div>
                <p class="mt-2 text-xs text-gray-500">0=Forgot, 5=Perfect recall</p>
            @else
                <div class="mt-6">
                    <button
                        wire:click="reveal"
                        class="px-6 py-3 text-white bg-black rounded hover:opacity-90"
                    >Show Answer</button>
                </div>
            @endif
        </div>
    @else
        <div class="p-8 border rounded bg-gray-50">
            🎉 All caught up! No due cards.
        </div>
    @endif

    <div>
        <a href="{{ route('decks.show', $deck) }}" class="text-sm underline">← Back to deck</a>
    </div>
</div>
