<?php

namespace App\Livewire\Study;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use App\Models\{Deck, Item, ReviewState};
use App\Services\SrsService;
use App\Enums\ReviewRating;

#[Layout('layouts.app')]
class StudyPanel extends Component
{
    use AuthorizesRequests;

    public Deck $deck;
    public ?Item $current = null;

    public string $mode = 'flashcard';
    public bool $showAnswer = false;

    public string $queueMode = 'mix';
    public int $reviewsThisSession = 0;
    public int $newThisSession = 0;
    public int $maxReviewsPerSession = 100;
    public int $maxNewPerSession = 20;

    public array $presets = [10, 50, 100];
    protected bool $currentWasNew = false;

    public int $dueRemaining = 0;
    public int $newRemaining = 0;
    public bool $sessionEnded = false;

    /** Queue tạm trong session học */
    protected Collection $queue;

    public function mount(Deck $deck): void
    {
        $this->authorize('view', $deck);
        $this->deck = $deck;
        $this->queue = collect();
        $this->refreshCounts();
        $this->loadNextItem();
    }

    public function render()
    {
        return view('livewire.study.study-panel');
    }

    // --- UI actions ---
    public function setPreset(int $n): void
    {
        if (!in_array($n, $this->presets, true)) return;
        $this->maxReviewsPerSession = $n;
        $this->maxNewPerSession = min($this->maxNewPerSession, $n);
    }

    public function setQueueMode(string $mode): void
    {
        $allowed = ['due', 'mix', 'new'];
        $this->queueMode = in_array($mode, $allowed, true) ? $mode : 'mix';
        $this->showAnswer = false;
        $this->queue = collect(); // reset queue khi đổi mode
        $this->loadNextItem();
    }

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

        // Nếu “Again” → thêm lại queue
        if ($rr === ReviewRating::AGAIN) {
            // có thể thay 0 => 2 để delay 2 thẻ
            $this->queue->splice(2, 0, [$this->current]);
        }

        $this->showAnswer = false;
        $this->refreshCounts();

        if ($this->reviewsThisSession >= $this->maxReviewsPerSession && $this->dueRemaining === 0) {
            $this->sessionEnded = true;
            return;
        }

        $this->loadNextItem();
    }

    public function nextCard(): void
    {
        $this->showAnswer = false;
        $this->loadNextItem();
    }

    protected function refreshCounts(): void
    {
        $userId = Auth::id();
        $now = Carbon::now();

        $this->dueRemaining = ReviewState::query()
            ->where('user_id', $userId)
            ->whereHas('item', fn($q) => $q->where('deck_id', $this->deck->id))
            ->where(fn($q) => $q->whereNull('due_at')->orWhere('due_at', '<=', $now))
            ->count();

        $this->newRemaining = Item::query()
            ->where('deck_id', $this->deck->id)
            ->whereDoesntHave('reviewStates', fn($q) => $q->where('user_id', $userId))
            ->count();
    }

    protected function loadNextItem(): void
    {
        // Ưu tiên lấy từ queue tạm (thẻ “Again”)
        if ($this->queue->isNotEmpty()) {
            $this->current = $this->queue->shift();
            return;
        }

        $this->current = null;
        $this->currentWasNew = false;
        $this->sessionEnded = false;

        $userId = Auth::id();
        $now = Carbon::now();

        if ($this->queueMode !== 'due' &&
            $this->reviewsThisSession >= $this->maxReviewsPerSession &&
            $this->dueRemaining === 0) {
            $this->sessionEnded = true;
            return;
        }

        $pickDue = fn() => ReviewState::query()
            ->where('user_id', $userId)
            ->whereHas('item', fn($q) => $q->where('deck_id', $this->deck->id))
            ->where(fn($q) => $q->whereNull('due_at')->orWhere('due_at', '<=', $now))
            ->orderBy('due_at', 'asc')->with('item')->first()?->item;

        $pickNew = fn() => Item::query()
            ->where('deck_id', $this->deck->id)
            ->whereDoesntHave('reviewStates', fn($q) => $q->where('user_id', $userId))
            ->orderBy('id')->first();

        $pickNextSoon = fn() => ReviewState::query()
            ->where('user_id', $userId)
            ->whereHas('item', fn($q) => $q->where('deck_id', $this->deck->id))
            ->whereNotNull('due_at')
            ->orderBy('due_at', 'asc')
            ->with('item')->first()?->item;

        switch ($this->queueMode) {
            case 'due':
                if ($due = $pickDue()) { $this->current = $due; return; }
                $this->sessionEnded = true; return;

            case 'new':
                if ($this->newThisSession >= $this->maxNewPerSession) { $this->sessionEnded = true; return; }
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
