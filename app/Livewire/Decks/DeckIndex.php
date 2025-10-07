<?php

namespace App\Livewire\Decks;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use Livewire\Attributes\Layout;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use App\Models\Deck;

#[Layout('layouts.app')]
class DeckIndex extends Component
{
    use AuthorizesRequests;
    use WithPagination;

    #[Url(as: 'q')]
    public string $q = '';

    public string $newName = '';

    /** Reset về trang 1 mỗi khi thay đổi từ khóa */
    public function updatingQ(): void
    {
        $this->resetPage();
    }

    /** Tạo deck mới */
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
        $this->resetPage(); // để deck mới (latest) nằm ở trang đầu
        session()->flash('ok', 'Deck created!');
    }

    /** Computed: danh sách deck theo user + keyword */
    public function getDecksProperty()
    {
        return Deck::query()
            ->where('user_id', Auth::id())
            ->when($this->q !== '', fn ($q) =>
                $q->where('name', 'like', '%' . $this->q . '%')
            )
            ->withCount('items')
            ->latest('id')
            ->paginate(12);
    }

    public function render()
    {
        return view('livewire.decks.deck-index', [
            'decks' => $this->decks, // gọi accessor getDecksProperty()
        ]);
    }
}
