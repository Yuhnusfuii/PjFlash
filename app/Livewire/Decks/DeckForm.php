<?php

namespace App\Livewire\Decks;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\Deck;

#[Layout('layouts.app')]
class DeckForm extends Component
{
    use AuthorizesRequests;

    /** null => create, có Deck => edit */
    public ?Deck $deck = null;
    public bool $isEdit = false;

    // Form fields
    public string $name = '';
    public ?string $description = null;
    public bool $is_public = false;

    // ⚠️ PHẢI nullable để /decks/create không 404
    public function mount(?Deck $deck = null): void
    {
        if (!$deck) {
            // $this->authorize('create', Deck::class); // mở nếu bạn dùng policy
            $this->isEdit = false;
            return;
        }

        $this->authorize('update', $deck);
        $this->deck        = $deck;
        $this->isEdit      = true;
        $this->name        = (string) $deck->name;
        $this->description = $deck->description;
        $this->is_public   = (bool) $deck->is_public;
    }

    public function save(): void
    {
        $this->validate([
            'name'        => ['required','string','max:255'],
            'description' => ['nullable','string'],
            'is_public'   => ['boolean'],
        ]);

        if ($this->deck) {
            $this->authorize('update', $this->deck);
            $this->deck->update([
                'name'        => $this->name,
                'description' => $this->description,
                'is_public'   => $this->is_public,
            ]);
            session()->flash('status', 'Updated!');
            $this->redirectRoute('decks.show', $this->deck->id, navigate: true);
            return;
        }

        // create
        // $this->authorize('create', Deck::class); // mở nếu dùng policy
        $deck = Deck::create([
            'user_id'     => auth()->id(),
            'name'        => $this->name,
            'description' => $this->description,
            'is_public'   => $this->is_public,
        ]);

        session()->flash('status', 'Created!');
        $this->redirectRoute('decks.show', $deck->id, navigate: true);
    }

    public function render()
    {
        // ✅ Render inline để bỏ phụ thuộc file blade (tránh 404 do view not found)
        return <<<'BLADE'
<div class="max-w-2xl mx-auto space-y-6 py-8">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-semibold">
            {{ $isEdit ? 'Edit deck' : 'Create deck' }}
        </h1>
        <a href="{{ route('decks.index') }}" class="y-btn">Back</a>
    </div>

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
BLADE;
    }
}
