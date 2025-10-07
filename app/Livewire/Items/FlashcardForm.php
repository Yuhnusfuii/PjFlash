<?php

namespace App\Livewire\Items;

use App\Models\Deck;
use App\Models\Item;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

#[Layout('layouts.app')]
class FlashcardForm extends Component
{
    use AuthorizesRequests;

    public Deck $deck;
    public ?Item $item = null;

    public string $front = '';
    public string $back  = '';
    public ?string $note = null;

    public bool $isEdit  = false;

    /**
     * Nhận tham số thô từ route:
     * - $deckId  = id của deck (number)
     * - $itemId  = id của item (number hoặc null)
     */
    public function mount(int $deckId, ?int $itemId = null): void
    {
        // Tự findOrFail thay vì implicit binding
        $this->deck = Deck::query()->findOrFail($deckId);

        // Deck thuộc user
        $this->authorize('view', $this->deck);

        if ($itemId !== null) {
            $this->item = Item::query()->findOrFail($itemId);

            // Bảo đảm item thuộc deck
            if ((int)$this->item->deck_id !== (int)$this->deck->id) {
                abort(404);
            }

            // Quyền update (có thể thay bằng $this->authorize('update', $this->item))
            $this->authorize('update', $this->deck);

            $this->front  = (string)($this->item->front ?? data_get($this->item, 'data.front', ''));
            $this->back   = (string)($this->item->back  ?? data_get($this->item, 'data.back', ''));
            $this->note   = $this->item->note ?? null;
            $this->isEdit = true;
        } else {
            // Tạo mới trong deck
            $this->authorize('create', [Item::class, $this->deck]);
            $this->isEdit = false;
        }
    }

    protected function rules(): array
    {
        return [
            'front' => ['required', 'string', 'max:5000'],
            'back'  => ['required', 'string', 'max:5000'],
            'note'  => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function save(): void
    {
        $this->validate();

        if ($this->isEdit && $this->item) {
            $payload = $this->item->data ?? [];
            $payload['front'] = $this->front;
            $payload['back']  = $this->back;

            $this->item->update([
                'front' => $this->front, // nếu schema có cột
                'back'  => $this->back,  // nếu schema có cột
                'note'  => $this->note,
                'data'  => $payload,
            ]);

            session()->flash('success', 'Flashcard updated.');
        } else {
            $this->item = Item::create([
                'deck_id' => $this->deck->id,
                'type'    => 'flashcard', // nếu schema có cột type
                'front'   => $this->front, // nếu schema có cột
                'back'    => $this->back,  // nếu schema có cột
                'note'    => $this->note,
                'data'    => ['front' => $this->front, 'back' => $this->back],
            ]);

            session()->flash('success', 'Flashcard created.');
            $this->isEdit = true;
        }

        // Điều hướng sau khi lưu: về trang edit
        $this->redirectRoute('flashcards.edit', [
            'deckId' => $this->deck->id,
            'itemId' => $this->item->id,
        ], navigate: true);
    }

    public function render()
    {
        return view('livewire.items.flashcard-form');
    }
}
