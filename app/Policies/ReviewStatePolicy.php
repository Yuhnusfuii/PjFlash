<?php

namespace App\Policies;

use App\Models\ReviewState;
use App\Models\User;

class ReviewStatePolicy
{
    public function view(User $user, ReviewState $state): bool
    {
        return $state->user_id === $user->id;
    }

    public function update(User $user, ReviewState $state): bool
    {
        return $state->user_id === $user->id;
    }
}
