<?php

namespace App\Services\Quiz;

use App\Models\Deck;
use App\Models\Item;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class McqGenerator
{
    /**
     * Tạo bộ câu hỏi MCQ từ 1 deck.
     *
     * @param  Deck   $deck  Deck nguồn
     * @param  int    $n     Số câu (tối đa theo số item)
     * @param  string $mode  mixed|front_to_back|back_to_front
     * @return array{questions: array<int, array>, meta: array}
     */
    public static function make(Deck $deck, int $n = 10, string $mode = 'mixed'): array
    {
        $items = $deck->items()->select(['id','front','back'])->get();

        if ($items->count() < 4) {
            return [
                'questions' => [],
                'meta' => [
                    'error' => 'Deck cần tối thiểu 4 thẻ để tạo câu hỏi.',
                ],
            ];
        }

        $pool = $items->shuffle()->take(min($n, $items->count()))->values();

        $questions = [];
        foreach ($pool as $item) {
            $direction = self::pickDirection($mode);

            [$promptField, $answerField] = $direction === 'front_to_back'
                ? ['front', 'back']
                : ['back', 'front'];

            $prompt  = trim((string) $item->{$promptField});
            $correct = trim((string) $item->{$answerField});

            // 3 đáp án nhiễu từ các item còn lại (theo cùng field của đáp án)
            $distractors = $items
                ->where('id', '!=', $item->id)
                ->pluck($answerField)
                ->filter(fn ($v) => filled($v) && trim((string) $v) !== $correct)
                ->shuffle()
                ->take(3)
                ->values()
                ->all();

            // nếu thiếu thì “bù” bằng các giá trị còn thiếu để đủ 3 (hiếm khi xảy ra)
            while (count($distractors) < 3) {
                $distractors[] = '—';
            }

            $options = $distractors;
            $options[] = $correct;
            shuffle($options);

            $correctIndex = array_search($correct, $options, true);

            $questions[] = [
                'item_id'      => $item->id,
                'direction'    => $direction,          // front_to_back | back_to_front
                'prompt'       => $prompt,             // câu hỏi (đề)
                'correct'      => $correct,            // đáp án đúng (text)
                'options'      => $options,            // mảng 4 đáp án
                'correctIndex' => $correctIndex,       // 0..3
            ];
        }

        return [
            'questions' => $questions,
            'meta' => [
                'deck_id' => $deck->id,
                'mode'    => $mode,
                'total'   => count($questions),
            ],
        ];

    }

    protected static function pickDirection(string $mode): string
    {
        return match ($mode) {
            'front_to_back' => 'front_to_back',
            'back_to_front' => 'back_to_front',
            default         => (rand(0,1) ? 'front_to_back' : 'back_to_front'),
        };
    }
    public static function makeGlobalForUser(int $userId, int $n = 10, string $mode = 'mixed'): array
    {
        // Lấy tất cả deck của user có >= 4 items
        $decks = Deck::query()
            ->where('user_id', $userId)
            ->with(['items:id,deck_id,front,back'])
            ->get()
            ->filter(fn ($d) => $d->items->count() >= 4)
            ->values();

        if ($decks->isEmpty()) {
            return [
                'questions' => [],
                'meta' => ['error' => 'Không có deck đủ điều kiện (>= 4 thẻ) để tạo Global MCQ.'],
            ];
        }

        // Gom toàn bộ item khả dụng làm pool câu hỏi
        $pool = $decks->flatMap->items;

        // Số câu tối đa
        $take = min($n, $pool->count());
        $pool = $pool->shuffle()->take($take)->values();

        $questions = [];

        foreach ($pool as $item) {
            // Xác định deck của item
            $deck = $decks->firstWhere('id', $item->deck_id);
            if (!$deck || $deck->items->count() < 4) {
                // skip item thuộc deck không đủ điều kiện (edge case)
                continue;
            }

            // Hướng hỏi
            $direction = match ($mode) {
                'front_to_back' => 'front_to_back',
                'back_to_front' => 'back_to_front',
                default => (rand(0,1) ? 'front_to_back' : 'back_to_front'),
            };

            [$promptField, $answerField] = $direction === 'front_to_back'
                ? ['front', 'back']
                : ['back', 'front'];

            $prompt  = trim((string) $item->{$promptField});
            $correct = trim((string) $item->{$answerField});

            // Distractors chỉ lấy từ CÙNG DECK với item
            $distractors = $deck->items
                ->where('id', '!=', $item->id)
                ->pluck($answerField)
                ->filter(fn ($v) => filled($v) && trim((string)$v) !== $correct)
                ->shuffle()
                ->take(3)
                ->values()
                ->all();

            while (count($distractors) < 3) {
                $distractors[] = '—';
            }

            $options = $distractors;
            $options[] = $correct;
            shuffle($options);

            $questions[] = [
                'item_id'      => $item->id,
                'deck_id'      => $deck->id,
                'deck_name'    => $deck->name,
                'direction'    => $direction,
                'prompt'       => $prompt,
                'correct'      => $correct,
                'options'      => $options,
                'correctIndex' => array_search($correct, $options, true),
            ];
        }

        return [
            'questions' => $questions,
            'meta' => [
                'mode'     => $mode,
                'total'    => count($questions),
                'kind'     => 'global',
            ],
        ];
    }
}

