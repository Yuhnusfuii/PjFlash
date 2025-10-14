<?php

namespace App\Livewire\Explore;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use App\Models\Deck;

#[Layout('layouts.app')]
class ExploreDecks extends Component
{
    #[Url(as: 'q')]
    public string $q = '';

    public function getDecksProperty()
    {
        return Deck::query()
            ->public()
            ->with(['items:id,deck_id', 'user:id,name']) // nếu có quan hệ user
            ->withCount('items')
            ->when($this->q !== '', fn($q) => $q->where('name', 'like', "%{$this->q}%"))
            ->latest('id')
            ->paginate(12);
    }

    public function render()
    {
        return view('livewire.explore.explore-decks', [
            'decks' => $this->decks,
        ]);
    }
}
