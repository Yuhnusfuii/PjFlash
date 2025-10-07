<?php

namespace App\Services;

use App\Enums\ReviewRating;
use App\Models\{Item, Review, ReviewState, User};
use Carbon\Carbon;

class SrsService
{
    public const DEFAULT_EASE = 2.50;  // SM-2 mặc định
    public const MIN_EASE     = 1.30;

    /** Tạo hoặc lấy ReviewState cho (user,item) */
    public function init(User $user, Item $item): ReviewState
    {
        // Ưu tiên set theo schema mới
        return ReviewState::firstOrCreate(
            ['user_id' => $user->id, 'item_id' => $item->id],
            [
                'ease_factor'   => self::DEFAULT_EASE,
                'interval_days' => 0,
                'repetitions'   => 0,
                'lapses'        => 0,
                'due_at'        => now(),
            ]
        );
    }

    /** Áp dụng SM-2 tối giản cho rating */
    public function review(User $user, Item $item, ReviewRating $rating, int $durationMs = 0): ReviewState
    {
        $state = $this->init($user, $item);

        // Map về 0..5 như SM-2 gốc (AGAIN=0, HARD=3, GOOD=4, EASY=5)
        $q = match ($rating) {
            ReviewRating::AGAIN => 0,
            ReviewRating::HARD  => 3,
            ReviewRating::GOOD  => 4,
            ReviewRating::EASY  => 5,
        };

        $now  = Carbon::now();

        // Lấy giá trị hiện tại theo schema mới (fallback sang cũ nếu null)
        $ef   = $state->ease_factor ?? $state->ease ?? self::DEFAULT_EASE;
        $rep  = (int) ($state->repetitions ?? 0);
        $laps = (int) ($state->lapses ?? 0);
        $int  = (int) ($state->interval_days ?? $state->interval ?? 0);

        // Công thức SM-2 cập nhật ease
        $ef = $ef + (0.1 - (5 - $q) * (0.08 + (5 - $q) * 0.02));
        if ($ef < self::MIN_EASE) $ef = self::MIN_EASE;

        if ($q < 3) {
            // Lapse
            $rep = 0;
            $laps++;
            $int = 1;
        } else {
            $rep++;
            if     ($rep === 1) $int = 1;
            elseif ($rep === 2) $int = 6;
            else                $int = (int) round($int * $ef);
        }

        $due = $now->copy()->addDays($int);

        // Lưu theo schema mới
        $state->fill([
            'ease_factor'      => $ef,
            'interval_days'    => $int,
            'repetitions'      => $rep,
            'lapses'           => $laps,
            'due_at'           => $due,
            'last_reviewed_at' => $now,
        ])->save();

        // Ghi log review nếu bạn có model Review (an toàn nếu đã có trong app)
        if (class_exists(Review::class)) {
            Review::create([
                'user_id'       => $user->id,
                'item_id'       => $item->id,
                'rating'        => $rating->value,
                'interval_days' => $int,
                'ease_factor'   => $ef,
                'reviewed_at'   => $now,
                'next_due_at'   => $due,
                'duration_ms'   => $durationMs,
                'meta'          => ['q' => $q],
            ]);
        }

        return $state;
    }
}
