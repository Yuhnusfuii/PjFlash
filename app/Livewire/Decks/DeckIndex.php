<?php

namespace App\Livewire\Decks;

use Livewire\Component;
use Livewire\Attributes\Url;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use App\Models\Deck;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class DeckIndex extends Component
{
    use AuthorizesRequests;

    #[Url(as: 'q')]
    public string $q = '';

    public string $newName = '';

    /**
     * Tạo deck mới.
     */
    public function createDeck(): void
    {
        $this->authorize('create', Deck::class);

        $this->validate([
            'newName' => ['required', 'string', 'max:255'],
        ]);

        Deck::create([
            'user_id'     => Auth::id(),
            'name'        => trim($this->newName),
            'description' => null,
        ]);

        $this->newName = '';
        session()->flash('ok', 'Deck created!');
    }

    /**
     * Computed property: danh sách deck theo user + keyword.
     */
    public function getDecksProperty()
    {
        return Deck::query()
            ->where('user_id', Auth::id())
            ->when($this->q !== '', fn ($q) =>
                $q->where('name', 'like', '%' . $this->q . '%')
            )
            ->latest('id')
            ->paginate(10);
    }

    public function render()
    {
        return view('livewire.decks.deck-index', [
            'decks' => $this->decks,
        ]);
    }
}
