<?php

namespace App\Livewire\Study;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use App\Models\{Item, ReviewState};
use App\Services\SrsService;
use App\Enums\ReviewRating;

#[Layout('layouts.app')]
class StudyAllPanel extends Component
{
    use AuthorizesRequests;

    public ?Item $current = null;

    public string $queueMode = 'mix';
    public string $mode = 'flashcard';
    public bool $showAnswer = false;

    public int $reviewsThisSession   = 0;
    public int $newThisSession       = 0;
    public int $maxReviewsPerSession = 100;
    public int $maxNewPerSession     = 20;

    public array $presets = [10, 50, 100];

    public int $dueRemaining = 0;
    public int $newRemaining = 0;

    public bool $sessionEnded = false;

    public function mount(): void
    {
        // chỉ user đăng nhập
        $this->authorize('viewAny', Item::class); // hoặc bỏ nếu policy chưa define
        $this->refreshCounts();
        $this->loadNextItem();
    }

    public function render()
    {
        return view('livewire.study.study-all-panel');
    }

    public function setPreset(int $n): void
    {
        if (!in_array($n, $this->presets, true)) return;
        $this->maxReviewsPerSession = $n;
        $this->maxNewPerSession = min($this->maxNewPerSession, $n);
    }

    public function setQueueMode(string $mode): void
    {
        $allowed = ['due','mix','new'];
        $this->queueMode = in_array($mode, $allowed, true) ? $mode : 'mix';
        $this->showAnswer = false;
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
        if (!$this->current->reviewStates()->where('user_id', $user->id)->exists()) {
            // an toàn: nếu là NEW trong lượt này (đã init trước đó)
            $this->newThisSession++;
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
        $now    = Carbon::now();

        $this->dueRemaining = ReviewState::query()
            ->where('user_id', $userId)
            ->where(function ($q) use ($now) {
                $q->whereNull('due_at')->orWhere('due_at', '<=', $now);
            })
            ->count();

        $this->newRemaining = Item::query()
            ->whereDoesntHave('reviewStates', fn($q) => $q->where('user_id', $userId))
            ->count();
    }

    protected function loadNextItem(): void
    {
        $this->current = null;
        $this->sessionEnded = false;

        $userId = Auth::id();
        $now    = Carbon::now();

        $pickDue = function () use ($userId, $now) {
            return ReviewState::query()
                ->where('user_id', $userId)
                ->where(function ($q) use ($now) {
                    $q->whereNull('due_at')->orWhere('due_at', '<=', $now);
                })
                ->orderBy('due_at', 'asc')
                ->with('item.deck')
                ->first()?->item;
        };

        $pickNew = function () use ($userId) {
            return Item::query()
                ->whereDoesntHave('reviewStates', fn($q) => $q->where('user_id', $userId))
                ->with('deck')
                ->orderBy('id')
                ->first();
        };

        $pickNextSoon = function () use ($userId) {
            return ReviewState::query()
                ->where('user_id', $userId)
                ->whereNotNull('due_at')
                ->orderBy('due_at', 'asc')
                ->with('item.deck')
                ->first()?->item;
        };

        switch ($this->queueMode) {
            case 'due':
                if ($due = $pickDue()) { $this->current = $due; return; }
                $this->sessionEnded = true; return;

            case 'new':
                if ($this->newThisSession >= $this->maxNewPerSession) { $this->sessionEnded = true; return; }
                if ($new = $pickNew()) {
                    app(SrsService::class)->init(Auth::user(), $new);
                    $this->current = $new; return;
                }
                $this->sessionEnded = true; return;

            default: // mix
                if ($due = $pickDue()) { $this->current = $due; return; }
                if ($this->newThisSession < $this->maxNewPerSession) {
                    if ($new = $pickNew()) {
                        app(SrsService::class)->init(Auth::user(), $new);
                        $this->current = $new; return;
                    }
                }
                if ($soon = $pickNextSoon()) { $this->current = $soon; return; }
                $this->sessionEnded = true; return;
        }
    }
}
