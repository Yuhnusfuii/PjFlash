<?php

namespace App\Services;

use App\Models\{Deck, Item};
use App\Services\Contracts\McqGeneratorServiceInterface;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class McqGeneratorService implements McqGeneratorServiceInterface
{
    public function generate(Item $item, ?Deck $context = null, int $numOptions = 4): array
    {
        // Câu hỏi & đáp án đúng (flashcard: front/back)
        $question = (string) ($item->front ?? '');
        $correct  = (string) ($item->back ?? '');

        // 1) Lấy pool distractor trong cùng deck (ưu tiên)
        $deckId = $context?->id ?? $item->deck_id;

        $pool = Item::query()
            ->where('type', 'flashcard')
            ->where('deck_id', $deckId)
            ->where('id', '!=', $item->id)
            ->pluck('back')
            ->filter()
            ->map(fn ($v) => (string) $v)
            ->unique()
            ->values();

        // 2) Fallback global nếu chưa đủ
        if ($pool->count() < $numOptions - 1) {
            $more = Item::query()
                ->where('type', 'flashcard')
                ->where('id', '!=', $item->id)
                ->pluck('back')
                ->filter()
                ->map(fn ($v) => (string) $v)
                ->unique()
                ->values();
            $pool = $pool->merge($more)->unique()->values();
        }

        // 3) Loại trùng với đáp án đúng & chọn đủ số lượng
        $pool = $pool->reject(fn ($opt) => $opt === $correct)->values();

        /** @var Collection $distractors */
        $distractors = $pool->shuffle()->take(max(0, $numOptions - 1));

        // 4) Gộp + xáo trộn, xác định answer index
        $options = $distractors->toArray();
        $options[] = $correct;
        $options = Arr::shuffle(array_values(array_unique($options)));

        $answerIndex = array_search($correct, $options, true);
        if ($answerIndex === false) {
            // Trường hợp hiếm khi $correct rỗng → đặt tạm index 0
            $answerIndex = 0;
        }

        return [
            'question' => $question !== '' ? $question : 'Question',
            'options'  => $options,
            'answer'   => $answerIndex,
            'meta'     => [
                'deck_id' => $deckId,
                'target_item_id' => $item->id,
            ],
        ];
    }
}
