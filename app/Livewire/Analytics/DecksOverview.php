<?php

namespace App\Livewire\Analytics;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use App\Models\Deck;
use App\Models\Item;

#[Layout('layouts.app')]
class DecksOverview extends Component
{
    use WithPagination;

    #[Url(as: 'q')]    public string $q = '';
    #[Url(as: 'sort')] public string $sort = 'due_desc'; // due_desc|due_asc|progress_desc|progress_asc|items_desc|items_asc|ef_desc|ef_asc|reviewed_desc|reviewed_asc
    #[Url(as: 'pp')]   public int $perPage = 10;

    protected $queryString = [];

    public function getRowsProperty()
    {
        $userId = Auth::id();
        $todayStart = Carbon::now()->startOfDay();

        $base = Deck::query()
            ->where('user_id', $userId)
            ->when($this->q !== '', fn($q) => $q->where('name', 'like', '%'.$this->q.'%'))
            // Tổng item
            ->withCount('items')
            // Đếm due now
            ->withCount(['items as due_now_count' => function ($q) {
                $q->where(function ($qq) {
                    $qq->whereNull('due_at')->orWhere('due_at', '<=', now());
                });
            }])
            // Đếm scheduled (tương lai)
            ->withCount(['items as scheduled_count' => function ($q) {
                $q->whereNotNull('due_at')->where('due_at', '>', now());
            }])
            // Learned (rep >= 1)
            ->withCount(['items as learned_count' => function ($q) {
                $q->where('repetition', '>=', 1);
            }])
            // Reviewed today
            ->withCount(['items as reviewed_today_count' => function ($q) use ($todayStart) {
                $q->where('last_reviewed_at', '>=', $todayStart);
            }])
            // Avg EF
            ->select('*')
            ->selectSub(function ($q) {
                $q->from('items')
                  ->selectRaw('COALESCE(AVG(ef), 0)')
                  ->whereColumn('items.deck_id', 'decks.id');
            }, 'avg_ef');

        // Sắp xếp
        $sortable = match ($this->sort) {
            'due_asc'       => ['due_now_count' => 'asc'],
            'due_desc'      => ['due_now_count' => 'desc'],
            'items_asc'     => ['items_count' => 'asc'],
            'items_desc'    => ['items_count' => 'desc'],
            'ef_asc'        => ['avg_ef' => 'asc'],
            'ef_desc'       => ['avg_ef' => 'desc'],
            'reviewed_asc'  => ['reviewed_today_count' => 'asc'],
            'reviewed_desc' => ['reviewed_today_count' => 'desc'],
            'progress_asc'  => [], // xử lý sau khi load (tính %), tạm sort theo learned_count/items_count proxy
            'progress_desc' => [],
            default         => ['due_now_count' => 'desc'],
        };

        foreach ($sortable as $col => $dir) {
            $base->orderBy($col, $dir);
        }

        // perPage an toàn
        $pp = in_array($this->perPage, [5,10,15,20,30,50], true) ? $this->perPage : 10;

        $paginator = $base->paginate($pp)->withQueryString();

        // Tính progress (%) sau khi load
        $paginator->getCollection()->transform(function ($deck) {
            $deck->progress = $deck->items_count > 0
                ? round(($deck->learned_count / $deck->items_count) * 100, 1)
                : 0.0;
            return $deck;
        });

        // Nếu sort theo progress, sort lại trên collection (nhẹ vì trong 1 page)
        if (str_starts_with($this->sort, 'progress_')) {
            $desc = $this->sort === 'progress_desc';
            $sorted = $paginator->getCollection()->sortBy('progress', SORT_REGULAR, $desc)->values();
            $paginator->setCollection($sorted);
        }

        return $paginator;
    }

    public function updatedQ()      { $this->resetPage(); }
    public function updatedSort()   { $this->resetPage(); }
    public function updatedPerPage(){ $this->resetPage(); }

    public function render()
    {
        return view('livewire.analytics.decks-overview', [
            'rows' => $this->rows,
        ]);
    }
}
