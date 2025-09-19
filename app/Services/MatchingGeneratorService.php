<?php

namespace App\Services;

use App\Models\{Deck, Item};
use App\Services\Contracts\MatchingGeneratorServiceInterface;
use Illuminate\Support\Arr;

class MatchingGeneratorService implements MatchingGeneratorServiceInterface
{
    public function generate(Item $item, ?Deck $context = null, int $numPairs = 4): array
    {
        $deckId = $context?->id ?? $item->deck_id;

        // 1) Lấy các cặp trong deck (ưu tiên)
        $query = Item::query()
            ->where('type', 'flashcard')
            ->where('deck_id', $deckId);

        // đưa target vào trước, sau đó thêm các item khác
        $items = collect([$item])
            ->merge(
                (clone $query)->where('id', '!=', $item->id)->inRandomOrder()->limit($numPairs * 3)->get()
            )
            ->unique('id')
            ->take($numPairs);

        // 2) Fallback global nếu chưa đủ cặp
        if ($items->count() < $numPairs) {
            $more = Item::query()
                ->where('type', 'flashcard')
                ->where('id', '!=', $item->id)
                ->inRandomOrder()
                ->limit($numPairs * 3)
                ->get();

            $items = $items->merge($more)->unique('id')->take($numPairs);
        }

        // 3) Dựng pairs: left = front, right = back
        $pairs = $items->map(function (Item $it) {
            return [
                'left'  => (string) ($it->front ?? ''),
                'right' => (string) ($it->back ?? ''),
            ];
        })->filter(fn ($p) => $p['left'] !== '' && $p['right'] !== '')
          ->unique(fn ($p) => $p['left'].'|'.$p['right'])
          ->values()
          ->all();

        // 4) Xáo trộn cho vui (UI có thể tiếp tục xáo hai cột)
        $pairs = Arr::shuffle($pairs);

        return [
            'pairs' => $pairs,
            'meta'  => [
                'deck_id' => $deckId,
                'target_item_id' => $item->id,
            ],
        ];
    }
}
