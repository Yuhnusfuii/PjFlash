<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Item;

class ItemController extends Controller
{
    /**
     * Display a listing of items.
     */
    public function index()
    {
        return response()->json(Item::all());
    }

    /**
     * Store a newly created item.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'deck_id' => 'required|integer|exists:decks,id',
            'front'   => 'required|string',
            'back'    => 'nullable|string',
        ]);

        $item = Item::create($data);

        return response()->json($item, 201);
    }

    /**
     * Display the specified item.
     */
    public function show(Item $item)
    {
        return response()->json($item);
    }

    /**
     * Update the specified item.
     */
    public function update(Request $request, Item $item)
    {
        $data = $request->validate([
            'front' => 'sometimes|string',
            'back'  => 'sometimes|string|nullable',
        ]);

        $item->update($data);

        return response()->json($item);
    }

    /**
     * Remove the specified item.
     */
    public function destroy(Item $item)
    {
        $item->delete();
        return response()->json(['message' => 'Item deleted successfully']);
    }
}
