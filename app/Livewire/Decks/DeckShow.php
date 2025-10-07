<?php

namespace App\Livewire\Decks;

use App\Models\Deck;
use App\Models\Item;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

#[Layout('layouts.app')]
class DeckShow extends Component
{
    use AuthorizesRequests;
    use WithPagination;

    public Deck $deck;

    // Modal states (đồng bộ Alpine <-> Livewire)
    public bool $showItemModal = false;
    public bool $showDeckModal = false;

    // Item đang chờ confirm xoá
    public ?int $confirmingItemId = null;

    protected $queryString = [
        'page' => ['except' => 1],
    ];

    public function mount(Deck $deck): void
    {
        $this->authorize('view', $deck);
        $this->deck = $deck;
    }

    public function getItemsProperty()
    {
        return $this->deck->items()->latest('id')->paginate(20);
    }

    public function newFlashcard(): void
    {
        $this->redirectRoute('flashcards.create', ['deckId' => $this->deck->id], navigate: true);
    }

    public function editDeck(): void
    {
        $this->redirectRoute('decks.edit', ['deck' => $this->deck->id], navigate: true);
    }

    public function study(): void
    {
        $this->redirectRoute('decks.study', ['deck' => $this->deck->id], navigate: true);
    }

    public function analytics(): void
    {
        $this->redirectRoute('decks.analytics', ['deck' => $this->deck->id], navigate: true);
    }

    /** Open deck delete modal */
    public function confirmDeleteDeck(): void
    {
        $this->showDeckModal = true;
    }

    /** Actually delete deck */
    public function deleteDeck(): void
    {
        $this->authorize('delete', $this->deck);
        $this->deck->delete();

        $this->showDeckModal = false;
        session()->flash('success', 'Deck deleted.');
        $this->redirectRoute('decks.index', navigate: true);
    }

    /** Open item delete modal */
    public function confirmDeleteItem(int $itemId): void
    {
        $this->confirmingItemId = $itemId;
        $this->showItemModal = true;
    }

    /** Actually delete item */
    public function deleteItem(): void
    {
        if (!$this->confirmingItemId) return;

        $item = Item::findOrFail($this->confirmingItemId);
        $this->authorize('delete', $item);

        if ($item->deck_id !== $this->deck->id) {
            abort(403);
        }

        $item->delete();

        $this->showItemModal = false;
        $this->confirmingItemId = null;

        session()->flash('success', 'Flashcard deleted.');
        $this->resetPage(); // refresh list (và tránh ở trang > tổng mới)
    }

    public function render()
    {
        return view('livewire.decks.deck-show', [
            'deck'  => $this->deck,
            'items' => $this->items,
        ]);
    }
}
