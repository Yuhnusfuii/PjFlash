<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Deck;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class FlashcardController extends Controller
{
    /**
     * GET /api/decks/{deck}/flashcards
     * Liệt kê flashcards trong 1 deck.
     */
    public function index(Request $request, Deck $deck)
    {
        Gate::authorize('view', $deck);

        $rows = Item::query()
            ->where('deck_id', $deck->id)
            ->where('type', 'flashcard')
            ->latest()
            ->paginate(50);

        return response()->json($rows);
    }

    /**
     * POST /api/decks/{deck}/flashcards
     * Tạo flashcard: data.front, data.back (JSON).
     */
    public function store(Request $request, Deck $deck)
    {
        Gate::authorize('update', $deck);

        $data = $request->validate([
            'front' => ['required', 'string'],
            'back'  => ['required', 'string'],
        ]);

        $item = Item::create([
            'deck_id' => $deck->id,
            'type'    => 'flashcard',
            'data'    => ['front' => $data['front'], 'back' => $data['back']],
        ]);

        return response()->json($item, 201);
    }

    /**
     * GET /api/flashcards/{flashcard}
     */
    public function show(Request $request, Item $flashcard)
    {
        Gate::authorize('view', $flashcard->deck);
        abort_unless($flashcard->type === 'flashcard', 404);

        return response()->json($flashcard);
    }

    /**
     * PUT /api/flashcards/{flashcard}
     * Edit front/back.
     */
    public function update(Request $request, Item $flashcard)
    {
        Gate::authorize('update', $flashcard->deck);
        abort_unless($flashcard->type === 'flashcard', 404);

        $data = $request->validate([
            'front' => ['sometimes', 'string'],
            'back'  => ['sometimes', 'string'],
        ]);

        $payload = $flashcard->data ?? [];
        if (array_key_exists('front', $data)) { $payload['front'] = $data['front']; }
        if (array_key_exists('back',  $data)) { $payload['back']  = $data['back'];  }

        $flashcard->data = $payload;
        $flashcard->save();

        return response()->json($flashcard);
    }

    /**
     * DELETE /api/flashcards/{flashcard}
     */
    public function destroy(Request $request, Item $flashcard)
    {
        Gate::authorize('delete', $flashcard->deck);
        abort_unless($flashcard->type === 'flashcard', 404);

        $flashcard->delete();
        return response()->json(['deleted' => true]);
    }
}
