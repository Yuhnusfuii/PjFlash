<?php

namespace App\Services;

use App\Models\{Deck, Item};

class MatchingGeneratorService
{
    public function generateFromDeck(Deck $deck, int $pairs = 4): ?array
    {
        $ids = Deck::where('id',$deck->id)->orWhere('parent_id',$deck->id)->pluck('id');

        $pool = Item::whereIn('deck_id',$ids)
            ->where('type','flashcard')
            ->whereNotNull('front')->whereNotNull('back')
            ->inRandomOrder()->limit($pairs)->get();

        if ($pool->count() < 2) return null;

        $pairsArr = $pool->map(fn($i) => [
            'left'=>(string)$i->front,
            'right'=>(string)$i->back,
            'source_item_id'=>$i->id,
        ])->toArray();

        $rights = array_column($pairsArr,'right');
        shuffle($rights);
        foreach ($pairsArr as $k=>&$p) $p['right'] = $rights[$k];

        return ['pairs'=>$pairsArr];
    }
}
