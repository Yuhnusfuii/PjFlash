<?php

namespace App\Livewire\Decks;

use App\Models\Deck;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class DeckForm extends Component
{
    use AuthorizesRequests;

    public ?Deck $deck = null;

    public string $name = '';
    public ?string $description = null;
    public bool $is_public = false;   // <-- NEW
    public bool $isEdit = false;

    public function mount(?Deck $deck = null): void
    {
        if ($deck) {
            $this->authorize('update', $deck);

            $this->deck        = $deck;
            $this->name        = (string) $deck->name;
            $this->description = $deck->description;
            $this->is_public   = (bool) $deck->is_public;  // <-- NEW
            $this->isEdit      = true;
        }
    }

    public function save(): void
    {
        $data = $this->validate([
            'name'        => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'is_public'   => ['boolean'], // <-- NEW
        ]);

        if ($this->isEdit && $this->deck) {
            $this->authorize('update', $this->deck);

            $this->deck->fill([
                'name'        => $data['name'],
                'description' => $data['description'] ?? null,
                'is_public'   => (bool) ($data['is_public'] ?? false), // <-- NEW
            ])->save();

            session()->flash('status', 'Deck updated.');
            $this->redirectRoute('decks.show', $this->deck);
            return;
        }

        $deck = Deck::create([
            'user_id'     => Auth::id(),
            'name'        => $data['name'],
            'description' => $data['description'] ?? null,
            'is_public'   => (bool) ($data['is_public'] ?? false), // <-- NEW
        ]);

        session()->flash('status', 'Deck created.');
        $this->redirectRoute('decks.show', $deck);
    }

    public function render()
    {
        return view('livewire.decks.deck-form');
    }
}
