<?php

namespace App\Http\Controllers;

use App\Enums\ReviewRating;
use App\Models\{Item, ReviewState};
use App\Services\Contracts\SrsServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;

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
    public function review(Request $request, Item $item, SrsServiceInterface $srs)
    {
        $data = $request->validate([
            'rating'      => ['required','string'], // 'again'|'hard'|'good'|'easy' (enum bước 7)
            'duration_ms' => ['nullable','integer','min:0'],
            'meta'        => ['nullable','array'],
        ]);

        // Convert string -> enum (tuỳ enum của bạn)
        $rating = ReviewRating::from(strtoupper($data['rating']));
        $duration = (int) ($data['duration_ms'] ?? 0);
        $meta = $data['meta'] ?? [];

        $state = $srs->review($request->user(), $item, $rating, $duration, $meta);

        return response()->json([
            'item_id'  => $item->id,
            'interval' => $state->interval,
            'ease'     => $state->ease,
            'due_at'   => $state->due_at,
        ], Response::HTTP_OK);
    }
}
