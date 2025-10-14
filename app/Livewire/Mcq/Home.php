<?php

namespace App\Livewire\Mcq;

use App\Models\Deck;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('layouts.app')]
class Home extends Component
{
    #[Url(as: 'q')]
    public string $q = '';

    public string $mode = 'mixed';     // mixed|front_to_back|back_to_front
    public int $num = 10;

    public ?int $deckId = null;

    public function updatedDeckId(): void
    {
        // no-op, chỉ để binding
    }

    public function start(): \Symfony\Component\HttpFoundation\Response
    {
        abort_unless($this->deckId, 404);
        $deck = Deck::query()->where('user_id', Auth::id())->findOrFail($this->deckId);

        return redirect()->route('mcq.take', [
            'deck' => $deck->id,
            'mode' => $this->mode,
            'n'    => $this->num,
        ]);
    }

    public function getDecksProperty()
    {
        return Deck::query()
            ->where('user_id', Auth::id())
            ->when($this->q !== '', fn ($q) =>
                $q->where('name','like','%'.$this->q.'%')
            )
            ->withCount('items')
            ->latest('id')
            ->paginate(12);
    }

    public function render()
    {
        return view('livewire.mcq.home', [
            'decks' => $this->decks,
        ]);
    }
}
