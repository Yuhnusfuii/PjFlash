<?php

namespace App\Policies;

use App\Models\Deck;
use App\Models\Item;
use App\Models\User;

class ItemPolicy
{
    public function view(User $user, Item $item): bool
    {
        return $item->deck && $item->deck->user_id === $user->id;
    }

    public function update(User $user, Item $item): bool
    {
        return $item->deck && $item->deck->user_id === $user->id;
    }

    public function delete(User $user, Item $item): bool
    {
        return $item->deck && $item->deck->user_id === $user->id;
    }

    // Tạo item trong 1 deck
    public function create(User $user, Deck $deck): bool
    {
        return $deck->user_id === $user->id;
    }
}
