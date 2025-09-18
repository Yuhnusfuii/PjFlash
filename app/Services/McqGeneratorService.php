<?php

namespace App\Services;

use App\Models\{Deck, Item};
use Illuminate\Support\Arr;

class McqGeneratorService
{
    public function generateFromDeck(Deck $deck, int $choices = 4): ?array
    {
        $ids = Deck::where('id',$deck->id)->orWhere('parent_id',$deck->id)->pluck('id');

        $pool = Item::whereIn('deck_id',$ids)
            ->where('type','flashcard')
            ->whereNotNull('front')->whereNotNull('back')
            ->inRandomOrder()->get();

        if ($pool->count() < 2) return null;

        $core    = $pool->random();
        $correct = trim((string) $core->back);

        $distractors = $pool->where('id','!=',$core->id)->pluck('back')
            ->map(fn($v)=>trim((string)$v))->unique()->values();

        if ($distractors->count() < ($choices-1)) {
            $global = Item::where('type','flashcard')->where('id','!=',$core->id)
                ->inRandomOrder()->limit(($choices-1)-$distractors->count())
                ->pluck('back')->map(fn($v)=>trim((string)$v));
            $distractors = $distractors->merge($global)->unique()->values();
        }

        $options = $distractors->take($choices-1)->toArray();
        $options[] = $correct;
        $options = Arr::shuffle($options);

        return [
            'question'=>(string)$core->front,
            'choices'=>$options,
            'correct_index'=>array_search($correct,$options,true),
            'explanation'=>null,
            'source_item_id'=>$core->id,
        ];
    }
}
