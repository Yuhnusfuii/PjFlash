<?php

namespace App\Livewire\Analytics;

use App\Models\Deck;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class DecksOverview extends Component
{
    use WithPagination;

    public string $search = '';
    public string $sortBy = 'due_now_count'; // mặc định sort theo due_now
    public string $sortDir = 'desc';          // asc|desc
    public int $perPage   = 10;

    protected $queryString = [
        'search'  => ['except' => ''],
        'sortBy'  => ['except' => 'due_now_count'],
        'sortDir' => ['except' => 'desc'],
        'page'    => ['except' => 1],
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function sort(string $field): void
    {
        if ($this->sortBy === $field) {
            $this->sortDir = $this->sortDir === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDir = 'desc';
        }
        $this->resetPage();
    }

    public function render()
    {
        $userId = Auth::id();
        $today  = Carbon::today();
        $now    = Carbon::now();

        $q = Deck::query()
            ->where('user_id', $userId)
            ->select('decks.*')

            // Tổng số item
            ->withCount(['items as total_items'])

            // Learned: repetition >= 1
            ->withCount([
                'items as learned_count' => fn($x) => $x->where('repetition', '>=', 1),
            ])

            // Reviewed today
            ->withCount([
                'items as reviewed_today_count' => fn($x) => $x->whereDate('last_reviewed_at', $today),
            ])

            // Due now = due_at NULL hoặc <= now
            ->selectSub(function ($sub) use ($now) {
                $sub->from('items')
                    ->selectRaw('COUNT(*)')
                    ->whereColumn('items.deck_id', 'decks.id')
                    ->where(function ($w) use ($now) {
                        $w->whereNull('due_at')->orWhere('due_at', '<=', $now);
                    });
            }, 'due_now_count')

            // Scheduled = due_at > now
            ->selectSub(function ($sub) use ($now) {
                $sub->from('items')
                    ->selectRaw('COUNT(*)')
                    ->whereColumn('items.deck_id', 'decks.id')
                    ->where('due_at', '>', $now);
            }, 'scheduled_count')

            // Avg EF
            ->selectSub(function ($sub) {
                $sub->from('items')
                    ->selectRaw('COALESCE(AVG(ef), 0)')
                    ->whereColumn('items.deck_id', 'decks.id');
            }, 'avg_ef');

        if ($this->search !== '') {
            $q->where('name', 'like', '%'.$this->search.'%');
        }

        $allowedSorts = [
            'name', 'total_items', 'learned_count', 'reviewed_today_count',
            'due_now_count', 'scheduled_count', 'avg_ef', 'updated_at', 'created_at'
        ];
        $sortBy  = in_array($this->sortBy, $allowedSorts, true) ? $this->sortBy : 'due_now_count';
        $sortDir = $this->sortDir === 'asc' ? 'asc' : 'desc';

        $decks = $q->orderBy($sortBy, $sortDir)
                   ->paginate($this->perPage);

        // Tính progress (%) ở PHP (learned/total)
        $decks->getCollection()->transform(function ($d) {
            $total   = (int) ($d->total_items ?? 0);
            $learned = (int) ($d->learned_count ?? 0);
            $d->progress_percent = $total > 0 ? round($learned / $total * 100) : 0;
            return $d;
        });

        return view('livewire.analytics.decks-overview', [
            'decks' => $decks,
            'rows'  => $decks,   // 👈 thêm alias để Blade cũ dùng @forelse($rows as $d)
        ]);
    }
}
