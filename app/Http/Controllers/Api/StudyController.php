<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\ReviewState;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\App;
use App\Enums\ReviewRating;
use App\Services\Contracts\SrsServiceInterface;

class StudyController extends Controller
{
    public function queue(Request $request): JsonResponse
    {
        $userId = $request->user()->id;

        $dueCount = ReviewState::where('user_id', $userId)
            ->where('due_at', '<=', Carbon::now())
            ->count();

        $newCount = Item::whereDoesntHave('reviewStates', fn($q) => $q->where('user_id', $userId))
            ->count();

        return response()->json(['due' => $dueCount, 'new' => $newCount]);
    }

    public function review(Item $item, Request $request): JsonResponse
    {
        $validated = $request->validate(['rating' => 'required|integer|min:0|max:5']);

        /** @var SrsServiceInterface $srs */
        $srs = App::make(SrsServiceInterface::class);
        $user = $request->user();

        // map int -> enum ReviewRating (fix lỗi kiểu enum)
        $rating = ReviewRating::tryFrom((int) $validated['rating']) ?? ReviewRating::AGAIN;

        $result = $srs->review($user, $item, $rating);

        return response()->json([
            'message' => 'Review recorded',
            'item_id' => $item->id,
            'rating'  => $rating, // enum
            'result'  => $result,
        ]);
    }
}
