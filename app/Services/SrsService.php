<?php

namespace App\Services;

use App\Enums\ReviewRating;
use App\Models\{Item, Review, ReviewState, User};
use Carbon\Carbon;

class SrsService
{
    public const DEFAULT_EASE = 2.5;
    public const MIN_EASE     = 1.3;

    public function init(User $user, Item $item): ReviewState
    {
        return ReviewState::firstOrCreate(
            ['user_id'=>$user->id, 'item_id'=>$item->id],
            ['ease'=>self::DEFAULT_EASE,'interval'=>0,'repetitions'=>0,'due_at'=>now()]
        );
    }

    public function review(User $user, Item $item, ReviewRating $rating, int $durationMs = 0): ReviewState
    {
        $state = $this->init($user, $item);

        $q = match ($rating) {
            ReviewRating::AGAIN => 0,
            ReviewRating::HARD  => 3,
            ReviewRating::GOOD  => 4,
            ReviewRating::EASY  => 5,
        };

        $now = Carbon::now();
        $ef  = $state->ease ?? self::DEFAULT_EASE;
        $rep = (int) $state->repetitions;
        $int = (int) $state->interval;

        $ef = $ef + (0.1 - (5 - $q) * (0.08 + (5 - $q) * 0.02));
        if ($ef < self::MIN_EASE) $ef = self::MIN_EASE;

        if ($q < 3) { $rep = 0; $int = 1; }
        else {
            $rep++;
            if ($rep === 1) $int = 1;
            elseif ($rep === 2) $int = 6;
            else $int = (int) round($int * $ef);
        }

        $due = $now->copy()->addDays($int);

        $state->fill([
            'ease'=>$ef,'interval'=>$int,'repetitions'=>$rep,
            'due_at'=>$due,'last_reviewed_at'=>$now,
        ])->save();

        Review::create([
            'user_id'=>$user->id,'item_id'=>$item->id,
            'rating'=>$rating->value,'interval_days'=>$int,'ease_factor'=>$ef,
            'reviewed_at'=>$now,'next_due_at'=>$due,'duration_ms'=>$durationMs,
            'meta'=>['q'=>$q],
        ]);

        return $state;
    }
}
