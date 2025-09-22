<?php

namespace App\Livewire\Decks;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\Deck;
use App\Models\Item;

#[Layout('layouts.app')]
class DeckShow extends Component
{
    use AuthorizesRequests;

    public Deck $deck;

    // Create inputs
    public string $front = '';
    public string $back  = '';

    // Edit inline state
    public ?int $editingId = null;
    public string $editFront = '';
    public string $editBack  = '';

    /**
     * Mount: ensure user can view this deck and preload items.
     */
    public function mount(Deck $deck): void
    {
        $this->authorize('view', $deck);
        $this->deck = $deck->load(['items' => fn ($q) => $q->latest()]);
    }

    /**
     * Create new flashcard item.
     */
    public function addItem(): void
    {
        $this->authorize('create', Item::class);

        $this->validate([
            'front' => ['required', 'string', 'max:500'],
            'back'  => ['required', 'string', 'max:2000'],
        ]);

        $this->deck->items()->create([
            'type'  => 'flashcard',
            'front' => trim($this->front),
            'back'  => trim($this->back),
            'data'  => null,
        ]);

        $this->front = $this->back = '';
        $this->refreshDeck();
        session()->flash('ok', 'Item added!');
    }

    /**
     * Start editing an item (load into edit form).
     */
    public function startEditItem(int $itemId): void
    {
        $item = Item::where('deck_id', $this->deck->id)->findOrFail($itemId);
        $this->authorize('update', $item);

        $this->editingId = $item->id;
        $this->editFront = $item->front ?? '';
        $this->editBack  = $item->back ?? '';
    }

    /**
     * Cancel edit state.
     */
    public function cancelEditItem(): void
    {
        $this->editingId = null;
        $this->editFront = '';
        $this->editBack  = '';
    }

    /**
     * Persist edits for the current item.
     */
    public function saveEditItem(): void
    {
        if (!$this->editingId) {
            return;
        }

        $this->validate([
            'editFront' => ['required', 'string', 'max:500'],
            'editBack'  => ['required', 'string', 'max:2000'],
        ]);

        $item = Item::where('deck_id', $this->deck->id)->findOrFail($this->editingId);
        $this->authorize('update', $item);

        $item->update([
            'front' => trim($this->editFront),
            'back'  => trim($this->editBack),
        ]);

        $this->cancelEditItem();
        $this->refreshDeck();
        session()->flash('ok', 'Item updated!');
    }

    /**
     * Delete an item.
     */
    public function deleteItem(int $itemId): void
    {
        $item = Item::where('deck_id', $this->deck->id)->findOrFail($itemId);
        $this->authorize('delete', $item);

        $item->delete();

        // nếu đang edit đúng item này thì thoát chế độ edit
        if ($this->editingId === $itemId) {
            $this->cancelEditItem();
        }

        $this->refreshDeck();
        session()->flash('ok', 'Item deleted!');
    }

    protected function refreshDeck(): void
    {
        $this->deck->refresh()->load(['items' => fn ($q) => $q->latest()]);
    }

    public function render()
    {
        // Không cần trả view cụ thể nếu bạn đã dùng Layout attribute và file view đúng tên:
        // resources/views/livewire/decks/deck-show.blade.php
        return view('livewire.decks.deck-show', ['deck' => $this->deck]);
    }
}
