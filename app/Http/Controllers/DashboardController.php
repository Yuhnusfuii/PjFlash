<?php

namespace App\Http\Controllers;

use App\Models\Deck;
use App\Models\Item;
use App\Models\ReviewState;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    public function __invoke(Request $request)
    {
        $uid = $request->user()->id;

        $deckCount = Deck::where('user_id', $uid)->count();

        $itemCount = Item::whereHas('deck', function ($q) use ($uid) {
            $q->where('user_id', $uid);
        })->count();

        $dueToday = ReviewState::where('user_id', $uid)
            ->where('due_at', '<=', now())
            ->count();

        $decks = Deck::withCount('items')
            ->where('user_id', $uid)
            ->latest('id')
            ->take(8)
            ->get();

        return view('dashboard', compact('deckCount', 'itemCount', 'dueToday', 'decks'));
    }
}
