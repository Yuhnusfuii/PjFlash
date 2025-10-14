<?php

namespace App\Livewire\Explore;

use Livewire\Attributes\Layout;
use Livewire\Component;
use App\Models\Deck;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

#[Layout('layouts.app')]
class PublicDeckShow extends Component
{
    public Deck $deck;

    public function mount(string $slug)
    {
        // Chỉ cho phép xem deck public theo slug
        $this->deck = Deck::query()
            ->public()
            ->where('slug', $slug)
            ->with(['items' => function ($q) {
                $q->select('id', 'deck_id', 'front', 'back')->latest('id');
            }])
            ->firstOrFail();
    }

    /**
     * Sao chép deck public về tài khoản hiện tại (fork)
     * - Name giữ nguyên; nếu trùng slug hệ thống tự tạo slug khác theo Deck::booted()
     * - is_public = false cho bản sao
     * - Sao chép toàn bộ items (front/back)
     */
    public function saveToMyDeck()
    {
        if (!Auth::check()) {
            // Không đăng nhập -> về login (giữ lại trang đích nếu bạn có middleware "auth" nâng cao)
            session()->flash('error', 'Hãy đăng nhập để lưu deck.');
            return $this->redirectRoute('login', navigate: true);
        }

        $userId = Auth::id();

        // Nếu bạn là chủ deck gốc (trường hợp share link cho chính mình) -> bỏ qua
        if ($this->deck->user_id === $userId) {
            session()->flash('ok', 'Deck này đã thuộc về bạn.');
            return;
        }

        $new = DB::transaction(function () use ($userId) {
            // Tạo deck mới
            /** @var Deck $copy */
            $copy = Deck::create([
                'user_id'     => $userId,
                'name'        => $this->deck->name,
                'description' => $this->deck->description,
                'is_public'   => false, // bản sao luôn private
            ]);

            // Sao chép items
            $bulk = $this->deck->items->map(function (Item $it) use ($copy) {
                return [
                    'deck_id'   => $copy->id,
                    'type'      => 'flashcard',   // giữ nguyên loại, nếu bạn có nhiều loại thì dùng $it->type
                    'front'     => $it->front,
                    'back'      => $it->back,
                    'created_at'=> now(),
                    'updated_at'=> now(),
                ];
            })->all();

            if (!empty($bulk)) {
                Item::insert($bulk);
            }

            return $copy->loadCount('items');
        });

        session()->flash('ok', "Đã lưu deck về tài khoản của bạn ({$new->items_count} items).");
        return $this->redirectRoute('decks.show', $new, navigate: true);
    }

    public function render()
    {
        return view('livewire.explore.public-deck-show');
    }
}
