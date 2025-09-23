<?php

namespace App\Http\Controllers;

use App\Models\{Deck, Item};
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use App\Http\Requests\{StoreItemRequest, UpdateItemRequest, ImportCsvRequest};


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

    public function store(StoreItemRequest $request)
    {
    $data = $request->validated();

    // Bảo đảm chỉ owner của deck được thêm item
    $deck = \App\Models\Deck::findOrFail($data['deck_id']);
    $this->authorize('update', $deck);

    $item = \App\Models\Item::create($data);

    return response()->json($item, \Illuminate\Http\Response::HTTP_CREATED);
    }

    public function show(Request $request, Item $item)
    {
        $this->authorize('view', $item); // sẽ tạo Policy
        return response()->json($item);
    }

    public function update(UpdateItemRequest $request, \App\Models\Item $item)
    {
    // Chỉ owner của deck chứa item được sửa
    $this->authorize('update', $item->deck);

    $item->update($request->validated());

    return response()->json($item);
    }

    public function destroy(Request $request, Item $item)
    {
        $this->authorize('delete', $item);

        $item->delete();

        return response()->noContent();
    }

    // Placeholder Import CSV (Bước 11 sẽ làm thật)
    public function import(ImportCsvRequest $request)
    {
    // Ở bước 11 mới xử lý parser + preview/commit
    return response()->json(['status' => 'accepted'], \Illuminate\Http\Response::HTTP_ACCEPTED);
    }
}
