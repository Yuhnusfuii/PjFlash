<?php

namespace App\Services\Contracts;

use App\Enums\ReviewRating;
use App\Models\{Item, User};
use App\Models\ReviewState;

interface SrsServiceInterface
{
    public function init(User $user, Item $item): ReviewState;

    public function review(
        User $user,
        Item $item,
        ReviewRating $rating,
        int $durationMs = 0,
        array $meta = []
    ): ReviewState;
}
