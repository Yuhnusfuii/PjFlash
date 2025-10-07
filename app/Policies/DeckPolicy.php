<?php

namespace App\Policies;

use App\Models\Deck;
use App\Models\User;

class DeckPolicy
{
    // app/Policies/DeckPolicy.php

    public function create(\App\Models\User $user): bool
    {
        // Cho phép mọi user đã đăng nhập tạo deck.
        // Nếu bạn muốn giới hạn (ví dụ <100 deck), kiểm tra ở đây.
        return true;
    }

    public function view(User $user, Deck $deck): bool
    {
        return $deck->user_id === $user->id;
    }

    public function update(User $user, Deck $deck): bool
    {
        return $deck->user_id === $user->id;
    }

    public function delete(User $user, Deck $deck): bool
    {
        return $deck->user_id === $user->id;
    }
}
