<?php

namespace App\Http\Controllers;

use App\Models\{Deck, Item};
use App\Services\Contracts\{
    McqGeneratorServiceInterface,
    MatchingGeneratorServiceInterface
};
use Illuminate\Http\Request;

class GeneratorController extends Controller
{
    public function mcq(Request $request, Item $item, McqGeneratorServiceInterface $gen)
    {
        $deck = null;
        if ($deckId = $request->integer('deck_id')) {
            $deck = Deck::find($deckId);
        }

        $num = max(2, min(8, $request->integer('num', 4)));
        $payload = $gen->generate($item, $deck, $num);

        return response()->json($payload);
    }

    public function matching(Request $request, Item $item, MatchingGeneratorServiceInterface $gen)
    {
        $deck = null;
        if ($deckId = $request->integer('deck_id')) {
            $deck = Deck::find($deckId);
        }

        $num = max(2, min(12, $request->integer('num', 4)));
        $payload = $gen->generate($item, $deck, $num);

        return response()->json($payload);
    }
}
