<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Deck;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class DeckController extends Controller
{
    /**
     * GET /api/decks
     * Danh sách deck của user (paginate 20).
     */
    public function index(Request $request)
    {
        $rows = Deck::query()
            ->where('user_id', $request->user()->id)
            ->latest()
            ->paginate(20);

        return response()->json($rows);
    }

    /**
     * POST /api/decks
     * Tạo deck mới.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        $deck = Deck::create([
            'user_id'     => $request->user()->id,
            'name'        => $data['name'],
            'description' => $data['description'] ?? null,
        ]);

        return response()->json($deck, 201);
    }

    /**
     * GET /api/decks/{deck}
     * (tuỳ nhu cầu)
     */
    public function show(Request $request, Deck $deck)
    {
        Gate::authorize('view', $deck);
        return response()->json($deck);
    }

    /**
     * PUT /api/decks/{deck}
     * Sửa deck.
     */
    public function update(Request $request, Deck $deck)
    {
        Gate::authorize('update', $deck);

        $data = $request->validate([
            'name'        => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        $deck->fill($data)->save();

        return response()->json($deck);
    }

    /**
     * DELETE /api/decks/{deck}
     * Xoá deck (và tuỳ cascade nếu đã set quan hệ DB).
     */
    public function destroy(Request $request, Deck $deck)
    {
        Gate::authorize('delete', $deck);
        $deck->delete();

        return response()->json(['deleted' => true]);
    }

    /**
     * POST /api/decks/{deck}/import
     * Placeholder để khớp rate-limiter 'import'.
     */
    public function import(Request $request, Deck $deck)
    {
        Gate::authorize('update', $deck);

        return response()->json([
            'message' => 'Import is not implemented yet.',
        ], 501);
    }
}
