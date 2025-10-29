<div class="max-w-2xl mx-auto space-y-6 py-8">

    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-semibold">
            {{ $isEdit ? 'Edit deck' : 'Create deck' }}
        </h1>

        <a href="{{ route('decks.index') }}" class="y-btn">Back</a>
    </div>

    {{-- Flash message --}}
    @if (session('status'))
        <div class="p-3 rounded-md border border-emerald-300 bg-emerald-50 text-emerald-700">
            {{ session('status') }}
        </div>
    @endif

    <div class="y-card y-card-pad">
        <form wire:submit.prevent="save" class="space-y-5">

            <div>
                <label class="y-label">Name</label>
                <input type="text" class="y-input" wire:model.defer="name" placeholder="My new deck">
                @error('name') <div class="text-rose-600 text-sm mt-1">{{ $message }}</div> @enderror
            </div>

            <div>
                <label class="y-label">Description</label>
                <textarea class="y-input" rows="4" wire:model.defer="description"
                          placeholder="Optional description..."></textarea>
                @error('description') <div class="text-rose-600 text-sm mt-1">{{ $message }}</div> @enderror
            </div>

            <div class="flex items-center gap-3">
                <input id="public" type="checkbox" class="rounded" wire:model.defer="is_public">
                <label for="public" class="text-sm text-slate-600">Public deck</label>
                @error('is_public') <div class="text-rose-600 text-sm">{{ $message }}</div> @enderror
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button class="y-btn y-btn--brand">
                    {{ $isEdit ? 'Save changes' : 'Create deck' }}
                </button>
                <a href="{{ $isEdit ? route('decks.show', $deck->id) : route('decks.index') }}"
                   class="y-btn">Cancel</a>
            </div>
        </form>
    </div>
</div>
