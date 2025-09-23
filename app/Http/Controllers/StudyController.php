<?php

namespace App\Http\Controllers;

use App\Enums\ReviewRating;
use App\Models\{Item, ReviewState};
use App\Services\Contracts\SrsServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use App\Http\Requests\ReviewRequest;


class StudyController extends Controller
{
    // GET /api/study/queue
    public function queue(Request $request)
    {
        $user = $request->user();

        $q = ReviewState::query()
            ->where('user_id', $user->id)
            ->whereNotNull('due_at')
            ->where('due_at', '<=', Carbon::now())
            ->orderBy('due_at')
            ->orderByDesc('laps');

        if ($deckId = $request->integer('deck_id')) {
            $q->whereHas('item', fn($qq) => $qq->where('deck_id', $deckId));
        }

        $limit = max(1, min(100, $request->integer('limit', 20)));

        $states = $q->with('item')->limit($limit)->get();

        return response()->json([
            'count' => $states->count(),
            'items' => $states->map(fn($st) => [
                'review_state_id' => $st->id,
                'item_id'         => $st->item_id,
                'type'            => $st->item->type,
                'front'           => $st->item->front,
                'back'            => $st->item->back, // UI có thể không show back cho flashcard
                'due_at'          => $st->due_at,
            ]),
        ]);
    }

    // POST /api/study/{item}/review
    public function review(
    ReviewRequest $request,
    \App\Models\Item $item
) {
    $data   = $request->validated();
    // KHÔNG đổi hoa/thường: enum của bạn là chuỗi 'again|hard|good|easy'
    $rating = \App\Enums\ReviewRating::from($data['rating']);
    $duration = (int)($data['duration_ms'] ?? 0);
    $meta   = $data['meta'] ?? [];

    // Lấy SRS service an toàn: ưu tiên interface, fallback concrete
    $srs = app()->bound(\App\Services\Contracts\SrsServiceInterface::class)
        ? app(\App\Services\Contracts\SrsServiceInterface::class)
        : app(\App\Services\SrsService::class);

    $state = $srs->review($request->user(), $item, $rating, $duration, $meta);

    return response()->json([
        'ok'    => true,
        'state' => [
            'ease_factor'   => $state->ease_factor ?? null,
            'interval_days' => $state->interval_days ?? null,
            'repetitions'   => $state->repetitions ?? null,
            'due_at'        => $state->due_at ?? null,
        ],
    ]);
}

}
