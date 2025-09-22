<?php

namespace App\Policies;

use App\Models\Deck;
use App\Models\User;

class DeckPolicy
{
    /**
     * Xem danh sách deck (index) — cho phép sau khi đăng nhập.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Xem 1 deck cụ thể — chỉ chủ sở hữu.
     */
    public function view(User $user, Deck $deck): bool
    {
        return $deck->user_id === $user->id;
    }

    /**
     * Tạo deck — cho phép người dùng đăng nhập.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Cập nhật deck — chỉ chủ sở hữu.
     */
    public function update(User $user, Deck $deck): bool
    {
        return $deck->user_id === $user->id;
    }

    /**
     * Xóa deck — chỉ chủ sở hữu.
     */
    public function delete(User $user, Deck $deck): bool
    {
        return $deck->user_id === $user->id;
    }

    // (Không bắt buộc) — nếu bạn có soft deletes thì mở các hàm dưới:
    public function restore(User $user, Deck $deck): bool
    {
        return $deck->user_id === $user->id;
    }

    public function forceDelete(User $user, Deck $deck): bool
    {
        return $deck->user_id === $user->id;
    }
}
