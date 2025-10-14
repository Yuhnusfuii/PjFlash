<?php

namespace App\Livewire\Study;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\Deck;

#[Layout('layouts.app')]
class McqHome extends Component
{
    use AuthorizesRequests;

    #[Url(as: 'q')]
    public string $q = '';

    public string $mode = 'mixed'; // mixed|front_to_back|back_to_front
    public int $num = 10;

    public ?int $deckId = null;

    /** Start theo deck đã chọn bằng radio (vẫn giữ để tương thích) */
    public function start()
    {
        abort_unless($this->deckId, 404);
        return $this->startDeck($this->deckId);
    }

    /** Start trực tiếp cho deck được bấm trên card */
    public function startDeck(int $deckId)
    {
        $deck = Deck::query()
            ->where('user_id', Auth::id())
            ->findOrFail($deckId);

        $this->authorize('view', $deck);

        return $this->redirectRoute('decks.mcq', [
            'deck' => $deck->id,
            'mode' => $this->mode,
            'n'    => $this->num,
        ], navigate: true);
    }

    public function getDecksProperty()
    {
        return Deck::query()
            ->where('user_id', Auth::id())
            ->when($this->q !== '', fn ($q) =>
                $q->where('name', 'like', "%{$this->q}%")
            )
            ->withCount('items')
            ->latest('id')
            ->paginate(12);
    }

    public function render()
    {
        return view('livewire.study.mcq-home', [
            'decks' => $this->decks,
        ]);
    }
}
