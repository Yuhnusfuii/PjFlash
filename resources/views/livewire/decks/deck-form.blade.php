<div class="max-w-2xl mx-auto">
    <h1 class="mb-6 text-2xl font-semibold">
        {{ $isEdit ? 'Edit deck' : 'Create deck' }}
    </h1>

    <form wire:submit.prevent="save" class="space-y-4">
        <div>
            <label class="block mb-1 text-sm font-medium">Name</label>
            <input type="text" wire:model.defer="name" class="w-full p-2 border rounded">
            @error('name') <div class="text-sm text-red-600">{{ $message }}</div> @enderror
        </div>

        <div>
            <label class="block mb-1 text-sm font-medium">Description</label>
            <textarea wire:model.defer="description" class="w-full p-2 border rounded" rows="4"></textarea>
            @error('description') <div class="text-sm text-red-600">{{ $message }}</div> @enderror
        </div>

        <div class="flex gap-2">
            <button class="px-4 py-2 text-white bg-black rounded">
                {{ $isEdit ? 'Save changes' : 'Create deck' }}
            </button>
            <a href="{{ $isEdit ? route('decks.show', $deck) : route('decks.index') }}" class="px-4 py-2 border rounded">
                Cancel
            </a>
        </div>
    </form>
</div>
