<?php

namespace App\Livewire\Decks;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\Deck;

#[Layout('layouts.app')]
class DeckCreateForm extends Component
{
    use AuthorizesRequests;

    // fields
    public string $name = '';
    public ?string $description = null;
    public bool $is_public = false;

    public function save(): void
    {
        // $this->authorize('create', Deck::class); // mở nếu bạn dùng policy

        $this->validate([
            'name'        => ['required','string','max:255'],
            'description' => ['nullable','string'],
            'is_public'   => ['boolean'],
        ]);

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
        return <<<'BLADE'
<div class="max-w-2xl mx-auto space-y-6 py-8">
  <div class="flex items-center justify-between">
    <h1 class="text-2xl font-semibold">Create deck</h1>
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
        <textarea class="y-input" rows="4" wire:model.defer="description" placeholder="Optional description..."></textarea>
        @error('description') <div class="text-rose-600 text-sm mt-1">{{ $message }}</div> @enderror
      </div>

      <div class="flex items-center gap-3">
        <input id="public" type="checkbox" class="rounded" wire:model.defer="is_public">
        <label for="public" class="text-sm text-slate-600">Public deck</label>
        @error('is_public') <div class="text-rose-600 text-sm">{{ $message }}</div> @enderror
      </div>

      <div class="flex items-center gap-3 pt-2">
        <button class="y-btn y-btn--brand">Create deck</button>
        <a href="{{ route('decks.index') }}" class="y-btn">Cancel</a>
      </div>
    </form>
  </div>
</div>
BLADE;
    }
}
