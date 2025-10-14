<div class="space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold">
            {{ $isEdit ? 'Edit Deck' : 'Create Deck' }}
        </h1>

        @if ($isEdit && $deck)
            <div class="flex items-center gap-2">
                @if ($deck->is_public)
                    {{-- Share link khi public --}}
                    <a href="{{ route('explore.show', $deck->slug) }}"
                       target="_blank"
                       class="btn-outline"
                       title="Open public link">
                        Public link
                    </a>
                @else
                    <span class="text-sm text-slate-500">Private</span>
                @endif
            </div>
        @endif
    </div>

    {{-- Form card --}}
    <div class="p-6 space-y-5 card">
        <div>
            <label class="block mb-1 text-sm font-medium">Name <span class="text-red-500">*</span></label>
            <input type="text" wire:model.defer="name" class="w-full px-3 py-2 border rounded-lg" placeholder="Deck name...">
            @error('name') <div class="mt-1 text-sm text-red-500">{{ $message }}</div> @enderror
        </div>

        <div>
            <label class="block mb-1 text-sm font-medium">Description</label>
            <textarea wire:model.defer="description" rows="3" class="w-full px-3 py-2 border rounded-lg" placeholder="Short description (optional)"></textarea>
            @error('description') <div class="mt-1 text-sm text-red-500">{{ $message }}</div> @enderror
        </div>

        <div class="flex items-center gap-3">
            <label class="inline-flex items-center gap-2">
                <input type="checkbox" wire:model.defer="is_public" class="rounded">
                <span>Public (show in Explore)</span>
            </label>

            @if ($isEdit && $deck && $is_public && $deck->slug)
                <div class="text-sm text-slate-500">
                    Share: <a class="text-sky-600 hover:underline" target="_blank" href="{{ route('explore.show', $deck->slug) }}">{{ route('explore.show', $deck->slug) }}</a>
                </div>
            @endif
        </div>

        <div class="flex items-center gap-3">
            <button wire:click="save" class="btn">
                {{ $isEdit ? 'Save changes' : 'Create deck' }}
            </button>
            <a href="{{ $isEdit && $deck ? route('decks.show', $deck) : route('decks.index') }}" class="btn-outline">
                Cancel
            </a>
        </div>
    </div>

    @if (session('status'))
        <div class="text-emerald-600">{{ session('status') }}</div>
    @endif
</div>
