<?php

namespace App\Livewire\Study;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use App\Models\{Deck, Item, ReviewState};
use App\Services\SrsService;
use App\Enums\ReviewRating;

#[Layout('layouts.app')]
class StudyPanel extends Component
{
    use AuthorizesRequests;

    public Deck $deck;
    public ?Item $current = null;

    // UI
    public string $mode = 'flashcard';
    public bool $showAnswer = false;

    // Queue: due | mix | new
    public string $queueMode = 'mix';

    // Session counters
    public int $reviewsThisSession = 0;
    public int $newThisSession     = 0;
    public int $maxReviewsPerSession = 100;
    public int $maxNewPerSession     = 20;

    public array $presets = [10, 50, 100];

    // Flags
    protected bool $currentWasNew = false;

    // Dashboard-ish
    public int $dueRemaining = 0;
    public int $newRemaining = 0;

    public bool $sessionEnded = false;

    /**
     * Hàng đợi tạm cho nút "Again" (mảng các item_id).
     * Dùng public + array để Livewire rehydrate an toàn.
     * @var int[]
     */
    public array $queue = [];

    public function mount(Deck $deck): void
    {
        $this->authorize('view', $deck);
        $this->deck = $deck;
        $this->queue = []; // bảo đảm đã init
        $this->refreshCounts();
        $this->loadNextItem();
    }

    public function render()
    {
        return view('livewire.study.study-panel');
    }

    // ===== UI actions =====
    public function setPreset(int $n): void
    {
        if (!in_array($n, $this->presets, true)) return;
        $this->maxReviewsPerSession = $n;
        $this->maxNewPerSession = min($this->maxNewPerSession, $n);
    }

    public function setMode(string $mode): void
    {
        $allowed = ['auto', 'flashcard'];
        $this->mode = in_array($mode, $allowed, true) ? $mode : 'flashcard';
        $this->showAnswer = false;
    }

    public function setQueueMode(string $mode): void
    {
        $allowed = ['due','mix','new'];
        $this->queueMode = in_array($mode, $allowed, true) ? $mode : 'mix';
        $this->showAnswer = false;

        // reset queue tạm khi đổi mode
        $this->queue = [];
        $this->loadNextItem();
    }

    /** Chấm điểm SRS */
    public function grade(int $rating, int $durationMs = 0): void
    {
        if (!$this->current) return;

        $user = Auth::user();

        $rr = match (true) {
            $rating <= 0                   => ReviewRating::AGAIN,
            $rating === 1                  => ReviewRating::HARD,
            $rating === 2 || $rating === 3 => ReviewRating::GOOD,
            default                        => ReviewRating::EASY,
        };

        app(SrsService::class)->review($user, $this->current, $rr, $durationMs);

        $this->reviewsThisSession++;
        if ($this->currentWasNew) $this->newThisSession++;

        // If AGAIN → đưa card quay lại hàng đợi (sau 2 thẻ nữa giống Anki)
        if ($rr === ReviewRating::AGAIN) {
            $id = $this->current->id;
            // chèn vào vị trí index 2 (sau 2 thẻ nữa)
            array_splice($this->queue, 2, 0, [$id]);
        }

        $this->showAnswer = false;
        $this->refreshCounts();

        // Dừng khi đạt trần & không còn due
        if ($this->queueMode !== 'due' &&
            $this->reviewsThisSession >= $this->maxReviewsPerSession &&
            $this->dueRemaining === 0) {
            $this->sessionEnded = true;
            return;
        }

        $this->loadNextItem();
    }

    public function refreshCurrent(): void
    {
        if ($this->current) $this->current->refresh();
    }

    public function nextCard(): void
    {
        $this->showAnswer = false;
        $this->loadNextItem();
    }

    // ===== Helpers =====
    protected function takeFromQueueIfAny(): bool
    {
        // bật while để bỏ qua ID không còn hợp lệ
        while (!empty($this->queue)) {
            $id = array_shift($this->queue);
            $item = Item::find($id);
            if ($item && $item->deck_id === $this->deck->id) {
                $this->current = $item;
                return true;
            }
        }
        return false;
    }

    // ===== Queue logic =====
    protected function refreshCounts(): void
    {
        $userId = Auth::id();
        $now    = Carbon::now();

        $this->dueRemaining = ReviewState::query()
            ->where('user_id', $userId)
            ->whereHas('item', fn($q) => $q->where('deck_id', $this->deck->id))
            ->where(function ($q) use ($now) {
                $q->whereNull('due_at')->orWhere('due_at', '<=', $now);
            })
            ->count();

        $this->newRemaining = Item::query()
            ->where('deck_id', $this->deck->id)
            ->whereDoesntHave('reviewStates', fn($q) => $q->where('user_id', $userId))
            ->count();
    }

    protected function loadNextItem(): void
    {
        // Ưu tiên thẻ nằm trong queue tạm (Again)
        if ($this->takeFromQueueIfAny()) {
            return;
        }

        $this->current = null;
        $this->currentWasNew = false;
        $this->sessionEnded = false;

        $userId = Auth::id();
        $now    = Carbon::now();

        if ($this->queueMode !== 'due' &&
            $this->reviewsThisSession >= $this->maxReviewsPerSession &&
            $this->dueRemaining === 0) {
            $this->sessionEnded = true;
            return;
        }

        $pickDue = function () use ($userId, $now) {
            return ReviewState::query()
                ->where('user_id', $userId)
                ->whereHas('item', fn($q) => $q->where('deck_id', $this->deck->id))
                ->where(function ($q) use ($now) {
                    $q->whereNull('due_at')->orWhere('due_at', '<=', $now);
                })
                ->orderBy('due_at', 'asc')
                ->with('item')
                ->first()?->item;
        };

        $pickNew = function () use ($userId) {
            return Item::query()
                ->where('deck_id', $this->deck->id)
                ->whereDoesntHave('reviewStates', fn($q) => $q->where('user_id', $userId))
                ->orderBy('id')
                ->first();
        };

        $pickNextSoon = function () use ($userId) {
            return ReviewState::query()
                ->where('user_id', $userId)
                ->whereHas('item', fn($q) => $q->where('deck_id', $this->deck->id))
                ->whereNotNull('due_at')
                ->orderBy('due_at', 'asc')
                ->with('item')
                ->first()?->item;
        };

        switch ($this->queueMode) {
            case 'due':
                if ($due = $pickDue()) { $this->current = $due; return; }
                $this->sessionEnded = true; return;

            case 'new':
                if ($this->newThisSession >= $this->maxNewPerSession) {
                    $this->sessionEnded = true; return;
                }
                if ($new = $pickNew()) {
                    app(SrsService::class)->init(Auth::user(), $new);
                    $this->current = $new;
                    $this->currentWasNew = true;
                    return;
                }
                $this->sessionEnded = true; return;

            default: // mix
                if ($due = $pickDue()) { $this->current = $due; return; }

                if ($this->newThisSession < $this->maxNewPerSession) {
                    if ($new = $pickNew()) {
                        app(SrsService::class)->init(Auth::user(), $new);
                        $this->current = $new;
                        $this->currentWasNew = true;
                        return;
                    }
                }

                if ($soon = $pickNextSoon()) { $this->current = $soon; return; }

                $this->sessionEnded = true; return;
        }
    }
}
