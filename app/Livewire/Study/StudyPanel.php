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

    // UI state
    // Study CHỈ còn Flashcard (SRS). Giữ 'auto' để không vỡ UI cũ nếu view còn nút.
    public string $mode = 'flashcard';
    public bool $showAnswer = false;

    // Queue mode: 'due' | 'mix' | 'new'
    public string $queueMode = 'mix';

    // Session counters/limits
    public int $reviewsThisSession   = 0;
    public int $newThisSession       = 0;
    public int $maxReviewsPerSession = 100;
    public int $maxNewPerSession     = 20;

    // Info for current pick
    protected bool $currentWasNew = false;

    // Dashboard-ish numbers
    public int $dueRemaining = 0;
    public int $newRemaining = 0;

    // End state
    public bool $sessionEnded = false;

    public function mount(Deck $deck): void
    {
        $this->authorize('view', $deck);
        $this->deck = $deck;
        $this->refreshCounts();
        $this->loadNextItem();
    }

    public function render()
    {
        return view('livewire.study.study-panel');
    }

    // ===== UI actions =====

    public function setMode(string $mode): void
    {
        // CHỈ cho phép 'flashcard' và 'auto' (ép mọi giá trị khác về 'flashcard')
        $allowed = ['auto', 'flashcard'];
        $this->mode = in_array($mode, $allowed, true) ? $mode : 'flashcard';
        $this->showAnswer = false;
    }

    public function setQueueMode(string $mode): void
    {
        $allowed = ['due','mix','new'];
        $this->queueMode = in_array($mode, $allowed, true) ? $mode : 'mix';
        $this->showAnswer = false;
        $this->loadNextItem();
    }

    /**
     * Nhận điểm từ UI và cập nhật SRS (Study chỉ Flashcard):
     * Map phím/nút UI về enum ReviewRating.
     */
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

        // Session counters
        $this->reviewsThisSession++;
        if ($this->currentWasNew) {
            $this->newThisSession++;
        }

        $this->showAnswer = false;
        $this->refreshCounts();
        $this->loadNextItem();
    }

    /** View muốn reload item hiện tại (an toàn giữ lại) */
    public function refreshCurrent(): void
    {
        if ($this->current) {
            $this->current->refresh();
        }
    }

    public function nextCard(): void
    {
        $this->showAnswer = false;
        $this->loadNextItem();
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

    /**
     * Logic lấy thẻ theo queue mode:
     *  - 'due' : chỉ DUE; nếu không còn → kết thúc phiên
     *  - 'new' : chỉ NEW; tôn trọng maxNewPerSession; nếu hết → kết thúc phiên
     *  - 'mix' : ưu tiên DUE → NEW (tôn trọng giới hạn) → next soon
     */
    protected function loadNextItem(): void
    {
        $this->current = null;
        $this->currentWasNew = false;
        $this->sessionEnded = false;

        $userId = Auth::id();
        $now    = Carbon::now();

        // Nếu đã chạm trần review & không còn due ⇒ end
        if ($this->queueMode !== 'due' &&
            $this->reviewsThisSession >= $this->maxReviewsPerSession &&
            $this->dueRemaining === 0) {
            $this->sessionEnded = true;
            return;
        }

        // Helper closures
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
                $due = $pickDue();
                if ($due) { $this->current = $due; return; }
                $this->sessionEnded = true;
                return;

            case 'new':
                if ($this->newThisSession >= $this->maxNewPerSession) {
                    $this->sessionEnded = true;
                    return;
                }
                $new = $pickNew();
                if ($new) {
                    app(SrsService::class)->init(Auth::user(), $new);
                    $this->current = $new;
                    $this->currentWasNew = true;
                    return;
                }
                $this->sessionEnded = true;
                return;

            default: // mix
                $due = $pickDue();
                if ($due) { $this->current = $due; return; }

                if ($this->newThisSession < $this->maxNewPerSession) {
                    $new = $pickNew();
                    if ($new) {
                        app(SrsService::class)->init(Auth::user(), $new);
                        $this->current = $new;
                        $this->currentWasNew = true;
                        return;
                    }
                }

                $soon = $pickNextSoon();
                if ($soon) { $this->current = $soon; return; }

                $this->sessionEnded = true;
                return;
        }
    }
}
