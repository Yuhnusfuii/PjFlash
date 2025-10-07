<div class="max-w-3xl mx-auto p-6">
    <h1 class="text-2xl font-semibold mb-4">
        {{ $isEdit ? 'Edit Flashcard' : 'Create Flashcard' }}
    </h1>

    @if (session('success'))
        <div class="mb-4 p-3 rounded bg-green-100 text-green-800">
            {{ session('success') }}
        </div>
    @endif

    <form wire:submit.prevent="save" class="space-y-4">
        <div>
            <label class="block text-sm font-medium mb-1">Front</label>
            <textarea wire:model.defer="front" rows="4" class="w-full border rounded p-2"></textarea>
            @error('front') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Back</label>
            <textarea wire:model.defer="back" rows="4" class="w-full border rounded p-2"></textarea>
            @error('back') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Note (optional)</label>
            <textarea wire:model.defer="note" rows="2" class="w-full border rounded p-2"></textarea>
            @error('note') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="flex gap-3">
            <button type="submit"
                    class="px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700">
                {{ $isEdit ? 'Save changes' : 'Create' }}
            </button>

            <a href="{{ route('decks.show', $deck->id) }}"
               class="px-4 py-2 rounded border hover:bg-gray-50">
               Back to Deck
            </a>
        </div>
    </form>
</div>
