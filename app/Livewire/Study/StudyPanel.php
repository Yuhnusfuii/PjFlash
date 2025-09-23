<?php

namespace App\Livewire\Study;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\Deck;
use App\Models\Item;

#[Layout('layouts.app')]
class StudyPanel extends Component
{
    use AuthorizesRequests;

    public Deck $deck;
    public ?Item $current = null;
    public bool $showAnswer = false;

    public function mount(Deck $deck): void
    {
        $this->authorize('view', $deck);
        $this->deck = $deck;
        $this->loadNextItem();
    }

    protected function loadNextItem(): void
    {
        $now = Carbon::now();
        $this->current = Item::where('deck_id', $this->deck->id)
            ->where(function ($q) use ($now) {
                $q->whereNull('due_at')->orWhere('due_at', '<=', $now);
            })
            ->orderBy('due_at','asc')
            ->first();

        $this->showAnswer = false;
    }

    public function reveal(): void
    {
        $this->showAnswer = true;
    }

    /**
     * Chấm điểm 0–5 theo SM-2
     */
    public function grade(int $q): void
    {
        if (!$this->current) return;

        $item = $this->current;

        // --- SM-2 Algorithm ---
        $ef = $item->ef;
        $rep = $item->repetition;
        $int = $item->interval;

        if ($q < 3) {
            $rep = 0;
            $int = 1;
        } else {
            if ($rep == 0) {
                $int = 1;
            } elseif ($rep == 1) {
                $int = 6;
            } else {
                $int = round($int * $ef);
            }
            $rep++;
        }

        // update EF
        $ef = $ef + (0.1 - (5 - $q) * (0.08 + (5 - $q) * 0.02));
        if ($ef < 1.3) $ef = 1.3;

        $item->update([
            'ef' => $ef,
            'interval' => $int,
            'repetition' => $rep,
            'due_at' => now()->addDays($int),
            'review_count' => $item->review_count + 1,
            'last_reviewed_at' => now(),
        ]);

        $this->loadNextItem();
    }

    public function render()
    {
        return view('livewire.study.study-panel', [
            'current' => $this->current,
        ]);
    }
}
