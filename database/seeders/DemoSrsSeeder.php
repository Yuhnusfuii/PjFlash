<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use App\Models\User;
use App\Models\Deck;
use App\Models\Item;

class DemoSrsSeeder extends Seeder
{
    public function run(): void
    {
        // 1) Tạo user demo (nếu chưa có)
        $demo = User::firstOrCreate(
            ['email' => 'demo@example.com'],
            [
                'name' => 'Demo User',
                'email_verified_at' => now(),
                'password' => Hash::make('password'), // 🔐 password: "password"
                'created_at' => now()->subDays(7),
                'updated_at' => now()->subDays(1),
            ]
        );

        // 2) Tạo thêm 1 user phụ
        $other = User::firstOrCreate(
            ['email' => 'other@example.com'],
            [
                'name' => 'Other User',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
            ]
        );

        // 3) Tạo 3 deck cho mỗi user
        $users = [$demo, $other];
        foreach ($users as $user) {
            Deck::factory()
                ->count(3)
                ->create(['user_id' => $user->id])
                ->each(function (Deck $deck) {
                    // 4) Mỗi deck sinh ~12 item, mix due_at để test StudyPanel
                    $items = Item::factory()
                        ->count(12)
                        ->create(['deck_id' => $deck->id]);

                    // 5) Tuỳ biến 3 item đầu tiên để có ví dụ rõ ràng
                    $now = Carbon::now();
                    $triplet = $items->take(3);
                    $customs = [
                        ['front' => 'Hello', 'back' => 'Xin chao', 'due_at' => $now->copy()->subDay()],
                        ['front' => 'Thank you', 'back' => 'Cam on', 'due_at' => $now],
                        ['front' => 'Goodbye', 'back' => 'Tam biet', 'due_at' => $now->copy()->addDays(2)],
                    ];
                    foreach ($triplet as $i => $it) {
                        $data = $customs[$i] ?? null;
                        if ($data) {
                            $it->update(array_merge([
                                'type' => 'flashcard',
                                'ef' => 2.5,
                                'interval' => 1,
                                'repetition' => 0,
                            ], $data));
                        }
                    }
                });
        }

        // 6) In hướng dẫn nhanh ra console
        $this->command->info('--- Demo data created ---');
        $this->command->info('Login with: demo@example.com / password');
        $this->command->info('Go to: /decks  → open any deck → /decks/{id}/study to test SRS');
    }
}
