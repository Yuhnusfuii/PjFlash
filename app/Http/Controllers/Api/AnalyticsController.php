<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Deck;
use App\Models\ReviewState;

class AnalyticsController extends Controller
{
    /**
     * Show analytics summary for a specific deck.
     */
    public function deckAnalytics(Deck $deck)
    {
        $totalItems = $deck->items()->count();
        $dueNow     = $deck->items()
            ->whereHas('reviewStates', function ($q) {
                $q->whereNull('due_at')->orWhere('due_at', '<=', now());
            })
            ->count();
        $learned    = $deck->items()
            ->whereHas('reviewStates', fn($q) => $q->where('repetition', '>=', 1))
            ->count();

        return response()->json([
            'deck'       => $deck->name,
            'totalItems' => $totalItems,
            'dueNow'     => $dueNow,
            'learned'    => $learned,
        ]);
    }

    /**
     * Overview of all decks for the authenticated user.
     */
    public function overview(Request $request)
    {
        $userId = $request->user()->id;
        $decks = Deck::where('user_id', $userId)->get();

        return response()->json($decks->map(function ($deck) {
            return [
                'deck'       => $deck->name,
                'items'      => $deck->items()->count(),
                'dueNow'     => $deck->items()
                    ->whereHas('reviewStates', function ($q) {
                        $q->whereNull('due_at')->orWhere('due_at', '<=', now());
                    })->count(),
            ];
        }));
    }
}
