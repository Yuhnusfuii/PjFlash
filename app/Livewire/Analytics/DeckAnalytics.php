<?php

namespace App\Livewire\Analytics;

use App\Models\Deck;
use App\Models\Item;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class DeckAnalytics extends Component
{
    public Deck $deck;

    // KPI
    public int $totalItems            = 0;
    public int $dueNowCount           = 0;
    public int $scheduledCount        = 0;
    public int $learnedCount          = 0;
    public int $reviewedTodayCount    = 0;
    public float $avgEf               = 0.0;

    // 7 ngày gần đây
    public array $dailyReviewedSeries = []; // [['label'=>'Mon', 'count'=>3, 'date'=>'2025-10-01'], ...]

    public function mount(Deck $deck): void
    {
        // bảo vệ quyền xem deck
        if ($deck->user_id !== Auth::id()) {
            abort(403);
        }
        $this->deck = $deck;
        $this->compute();
    }

    protected function compute(): void
    {
        $now   = Carbon::now();
        $today = Carbon::today();

        $base = Item::query()->where('deck_id', $this->deck->id);

        // Tổng
        $this->totalItems = (clone $base)->count();

        // Learned: repetition >= 1
        $this->learnedCount = (clone $base)->where('repetition', '>=', 1)->count();

        // Reviewed today
        $this->reviewedTodayCount = (clone $base)->whereDate('last_reviewed_at', $today)->count();

        // Due now (due_at null hoặc <= now)
        $this->dueNowCount = (clone $base)
            ->where(function ($q) use ($now) {
                $q->whereNull('due_at')->orWhere('due_at', '<=', $now);
            })
            ->count();

        // Scheduled (due_at > now)
        $this->scheduledCount = (clone $base)
            ->where('due_at', '>', $now)
            ->count();

        // Avg EF
        $this->avgEf = (float) (clone $base)->avg('ef') ?: 0.0;

        // Chuỗi 7 ngày (từ 6 ngày trước -> hôm nay)
        $series = [];
        for ($i = 6; $i >= 0; $i--) {
            $day = Carbon::today()->subDays($i);
            $count = (clone $base)->whereDate('last_reviewed_at', $day)->count();
            $series[] = [
                'date'  => $day->toDateString(),
                'label' => $day->format('D'),
                'count' => (int) $count,
            ];
        }
        $this->dailyReviewedSeries = $series;
    }

    public function render()
    {
        return view('livewire.analytics.deck-analytics');
    }
}
