<?php

namespace App\Http\Controllers;

use App\Models\Deck;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Requests\{StoreDeckRequest, UpdateDeckRequest};
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

   public function store(StoreDeckRequest $request)
    {
    $data = $request->validated();

    $deck = \App\Models\Deck::create($data + [
        'user_id' => $request->user()->id,
    ]);

    return response()->json($deck, \Illuminate\Http\Response::HTTP_CREATED);
    }

    public function show(Request $request, Deck $deck)
    {
        $this->authorize('view', $deck); // sẽ tạo Policy ở bước 8
        $deck->load('items');

        return response()->json($deck);
    }

    public function update(UpdateDeckRequest $request, \App\Models\Deck $deck)
    {
    $this->authorize('update', $deck);

    $deck->update($request->validated());

    return response()->json($deck);
    }

    public function destroy(Request $request, Deck $deck)
    {
        $this->authorize('delete', $deck);

        $deck->delete();

        return response()->noContent();
    }
}
