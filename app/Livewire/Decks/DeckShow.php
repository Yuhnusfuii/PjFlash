<?php

namespace App\Livewire\Decks;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\Deck;
use App\Models\Item;

#[Layout('layouts.app')]
class DeckShow extends Component
{
    use AuthorizesRequests, WithPagination;

    public Deck $deck;

    // Search / sort / pagination (giữ từ bước 2)
    #[Url(as: 'q')]    public string $q = '';
    #[Url(as: 'sort')] public string $sort = 'latest';   // latest|oldest|front_az|front_za
    #[Url(as: 'pp')]   public int $perPage = 10;

    // STATE cho modal Create
    public bool $showCreate = false;
    public string $createFront = '';
    public string $createBack  = '';

    // STATE cho modal Edit
    public bool $showEdit = false;
    public ?int $editId = null;
    public string $editFront = '';
    public string $editBack  = '';

    protected $queryString = [];

    public function mount(Deck $deck): void
    {
        $this->authorize('view', $deck);
        $this->deck = $deck;
    }

    /* ========== Datasource ========== */
    public function getItemsProperty()
    {
        $q = Item::query()->where('deck_id', $this->deck->id);

        if ($this->q !== '') {
            $term = '%' . $this->q . '%';
            $q->where(function ($qq) use ($term) {
                $qq->where('front', 'like', $term)
                   ->orWhere('back', 'like', $term)
                   ->orWhere('type', 'like', $term);
            });
        }

        $q = match ($this->sort) {
            'oldest'   => $q->orderBy('id', 'asc'),
            'front_az' => $q->orderBy('front', 'asc'),
            'front_za' => $q->orderBy('front', 'desc'),
            default    => $q->orderBy('id', 'desc'),
        };

        $pp = in_array($this->perPage, [5,10,15,20,30,50], true) ? $this->perPage : 10;

        return $q->paginate($pp)->withQueryString();
    }

    /* ========== Open/Close modals ========== */
    public function openCreate(): void
    {
        $this->authorize('create', Item::class);
        $this->resetValidation();
        $this->createFront = $this->createBack = '';
        $this->showCreate = true;
    }

    public function openEdit(int $itemId): void
    {
        $item = Item::where('deck_id', $this->deck->id)->findOrFail($itemId);
        $this->authorize('update', $item);

        $this->resetValidation();
        $this->editId    = $item->id;
        $this->editFront = $item->front ?? '';
        $this->editBack  = $item->back ?? '';
        $this->showEdit  = true;
    }

    public function closeCreate(): void
    {
        $this->showCreate = false;
    }

    public function closeEdit(): void
    {
        $this->showEdit = false;
        $this->editId = null;
        $this->editFront = $this->editBack = '';
    }

    /* ========== CRUD ========== */
    public function storeItem(): void
    {
        $this->authorize('create', Item::class);

        $this->validate([
            'createFront' => ['required', 'string', 'max:500'],
            'createBack'  => ['required', 'string', 'max:2000'],
        ]);

        $this->deck->items()->create([
            'type'  => 'flashcard',
            'front' => trim($this->createFront),
            'back'  => trim($this->createBack),
            'data'  => null,
        ]);

        $this->closeCreate();
        $this->resetPage(); // quay về trang 1 để thấy item mới
        session()->flash('ok', 'Item created!');
    }

    public function updateItem(): void
    {
        if (!$this->editId) return;

        $this->validate([
            'editFront' => ['required', 'string', 'max:500'],
            'editBack'  => ['required', 'string', 'max:2000'],
        ]);

        $item = Item::where('deck_id', $this->deck->id)->findOrFail($this->editId);
        $this->authorize('update', $item);

        $item->update([
            'front' => trim($this->editFront),
            'back'  => trim($this->editBack),
        ]);

        $this->closeEdit();
        session()->flash('ok', 'Item updated!');
    }

    public function deleteItem(int $itemId): void
    {
        $item = Item::where('deck_id', $this->deck->id)->findOrFail($itemId);
        $this->authorize('delete', $item);

        $item->delete();

        // Nếu xóa hết ở trang hiện tại, lùi 1 trang
        if ($this->items->count() === 0 && $this->page > 1) {
            $this->previousPage();
        }

        // Nếu đang edit đúng item, đóng modal
        if ($this->editId === $itemId) {
            $this->closeEdit();
        }

        session()->flash('ok', 'Item deleted!');
    }

    /* ========== UI handlers ========== */
    public function updatedQ(): void      { $this->resetPage(); }
    public function updatedSort(): void   { $this->resetPage(); }
    public function updatedPerPage(): void{ $this->resetPage(); }

    public function render()
    {
        return view('livewire.decks.deck-show', [
            'items' => $this->items,
        ]);
    }
    public function getDueCountProperty(): int
{
    return \App\Models\Item::where('deck_id', $this->deck->id)
        ->where(function ($q) {
            $q->whereNull('due_at')->orWhere('due_at', '<=', now());
        })
        ->count();
}

}
