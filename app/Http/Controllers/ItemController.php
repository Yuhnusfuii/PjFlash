<?php

namespace App\Http\Controllers;

use App\Models\{Deck, Item};
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $query = Item::query()
            ->whereHas('deck', fn($q) => $q->where('user_id', $request->user()->id))
            ->latest('id');

        if ($deckId = $request->integer('deck_id')) {
            $query->where('deck_id', $deckId);
        }

        return response()->json($query->paginate(20));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'deck_id' => ['required','integer','exists:decks,id'],
            'type'    => ['required', Rule::in(['flashcard','mcq','matching'])], // nếu bạn dùng Enum thì đổi cho khớp
            'front'   => ['nullable','string'],
            'back'    => ['nullable','string'],
            'data'    => ['nullable','array'],
        ]);

        // quyền theo Deck (bước 8 thêm Policy)
        $deck = Deck::findOrFail($data['deck_id']);
        $this->authorize('update', $deck);

        $item = Item::create($data);

        return response()->json($item, Response::HTTP_CREATED);
    }

    public function show(Request $request, Item $item)
    {
        $this->authorize('view', $item); // sẽ tạo Policy
        return response()->json($item);
    }

    public function update(Request $request, Item $item)
    {
        $this->authorize('update', $item);

        $data = $request->validate([
            'type'  => ['sometimes', Rule::in(['flashcard','mcq','matching'])],
            'front' => ['nullable','string'],
            'back'  => ['nullable','string'],
            'data'  => ['nullable','array'],
        ]);

        $item->update($data);

        return response()->json($item);
    }

    public function destroy(Request $request, Item $item)
    {
        $this->authorize('delete', $item);

        $item->delete();

        return response()->noContent();
    }

    // Placeholder Import CSV (Bước 11 sẽ làm thật)
    public function import(Request $request)
    {
        $request->validate([
            'file' => ['required','file','mimes:csv,txt'],
        ]);

        // TODO: parse preview/commit ở Bước 11
        return response()->json(['status' => 'accepted'], Response::HTTP_ACCEPTED);
    }
}
