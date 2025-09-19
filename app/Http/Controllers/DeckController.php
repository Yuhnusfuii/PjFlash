<?php

namespace App\Http\Controllers;

use App\Models\Deck;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class DeckController extends Controller
{
    public function index(Request $request)
    {
        $decks = Deck::query()
            ->where('user_id', $request->user()->id)
            ->withCount('items')
            ->latest('id')
            ->paginate(20);

        return response()->json($decks);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => ['required','string','max:255'],
            'description' => ['nullable','string','max:1000'],
            'parent_id'   => ['nullable','integer','exists:decks,id'],
        ]);

        $deck = Deck::create($data + ['user_id' => $request->user()->id]);

        return response()->json($deck, Response::HTTP_CREATED);
    }

    public function show(Request $request, Deck $deck)
    {
        $this->authorize('view', $deck); // sẽ tạo Policy ở bước 8
        $deck->load('items');

        return response()->json($deck);
    }

    public function update(Request $request, Deck $deck)
    {
        $this->authorize('update', $deck);

        $data = $request->validate([
            'name'        => ['sometimes','string','max:255'],
            'description' => ['nullable','string','max:1000'],
            'parent_id'   => ['nullable','integer','exists:decks,id'],
        ]);

        $deck->update($data);

        return response()->json($deck);
    }

    public function destroy(Request $request, Deck $deck)
    {
        $this->authorize('delete', $deck);

        $deck->delete();

        return response()->noContent();
    }
}
