<?php

namespace App\Livewire\Analytics;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Carbon;
use App\Models\Deck;
use App\Models\Item;

#[Layout('layouts.app')]
class DeckAnalytics extends Component
{
    use AuthorizesRequests;

    public Deck $deck;

    // outputs
    public int $total = 0;
    public int $dueNow = 0;
    public int $scheduled = 0;
    public int $learned = 0;     // repetition >= 1
    public float $avgEf = 0.0;
    public int $reviewedToday = 0;

    /** mini chart: reviews per day (7 ngày gần nhất) */
    public array $daily = []; // [['date' => '2025-09-16', 'count' => 4], ...]

    public function mount(Deck $deck): void
    {
        $this->authorize('view', $deck);
        $this->deck = $deck;
        $this->compute();
    }

    protected function compute(): void
    {
        $now = Carbon::now();
        $startToday = $now->copy()->startOfDay();

        $q = Item::query()->where('deck_id', $this->deck->id);

        $this->total     = (clone $q)->count();
        $this->dueNow    = (clone $q)->where(function ($qq) use ($now) {
                            $qq->whereNull('due_at')->orWhere('due_at', '<=', $now);
                        })->count();
        $this->scheduled = (clone $q)->whereNotNull('due_at')->where('due_at', '>', $now)->count();
        $this->learned   = (clone $q)->where('repetition', '>=', 1)->count();
        $this->avgEf     = round((float)(clone $q)->avg('ef') ?? 0, 2);
        $this->reviewedToday = (clone $q)->where('last_reviewed_at', '>=', $startToday)->count();

        // 7 ngày gần nhất
        $this->daily = [];
        for ($i = 6; $i >= 0; $i--) {
            $dStart = $now->copy()->subDays($i)->startOfDay();
            $dEnd   = $now->copy()->subDays($i)->endOfDay();

            $count = (clone $q)->whereBetween('last_reviewed_at', [$dStart, $dEnd])->count();
            $this->daily[] = [
                'date'  => $dStart->toDateString(),
                'count' => $count,
            ];
        }
    }

    public function render()
    {
        return view('livewire.analytics.deck-analytics');
    }
}
