<?php

namespace App\Policies;

use App\Models\Item;
use App\Models\User;

class ItemPolicy
{
    /**
     * Determine whether the user can view the item.
     */
    public function view(User $user, Item $item): bool
    {
        return $item->deck && $item->deck->user_id === $user->id;
    }

    /**
     * Determine whether the user can update the item.
     */
    public function update(User $user, Item $item): bool
    {
        return $item->deck && $item->deck->user_id === $user->id;
    }

    /**
     * Determine whether the user can delete the item.
     */
    public function delete(User $user, Item $item): bool
    {
        return $item->deck && $item->deck->user_id === $user->id;
    }

    /**
     * Determine whether the user can create an item.
     */
    public function create(User $user): bool
    {
        return true;
    }
}
