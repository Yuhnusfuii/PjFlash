<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Deck;
use App\Models\Item;
use App\Models\ReviewState;
use App\Enums\ItemType;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        // 1) User demo
        $user = User::firstOrCreate(
            ['email' => 'demo@pjflash.test'],
            ['name' => 'Demo User', 'password' => Hash::make('password'), 'email_verified_at' => now()]
        );

        // 2) Deck tree
        $root = Deck::create([
            'user_id' => $user->id,
            'name' => 'Japanese N5',
            'description' => 'Demo deck with 3 item types',
        ]);

        $hiragana = Deck::create([
            'user_id' => $user->id, 'parent_id' => $root->id,
            'name' => 'Hiragana', 'description' => 'Basic hiragana flashcards',
        ]);

        $vocab = Deck::create([
            'user_id' => $user->id, 'parent_id' => $root->id,
            'name' => 'Vocabulary', 'description' => 'MCQ vocabulary',
        ]);

        $numbers = Deck::create([
            'user_id' => $user->id, 'parent_id' => $root->id,
            'name' => 'Numbers', 'description' => 'Matching pairs',
        ]);

        // 3) FLASHCARD
        $flashcards = [
            ['front'=>'あ','back'=>'a'],
            ['front'=>'い','back'=>'i'],
            ['front'=>'う','back'=>'u'],
            ['front'=>'え','back'=>'e'],
            ['front'=>'お','back'=>'o'],
        ];
        foreach ($flashcards as $i => $fc) {
            $item = Item::create([
                'deck_id' => $hiragana->id,
                'type' => ItemType::FLASHCARD,
                'front' => $fc['front'],
                'back'  => $fc['back'],
            ]);
            ReviewState::create([
                'user_id' => $user->id,
                'item_id' => $item->id,
                'ease' => 2.5,
                'interval' => 0,
                'repetitions' => 0,
                'due_at' => now()->subHours(3 - $i), // vài thẻ quá hạn để có hàng ôn
            ]);
        }

        // 4) MCQ
        $mcqs = [
            [
                'question' => '「猫」 nghĩa là gì?',
                'choices' => ['con chó','con mèo','con chim','con cá'],
                'correct_index' => 1,
                'explanation' => '猫 (neko) = con mèo',
            ],
            [
                'question' => '「水」 là?',
                'choices' => ['nước','lửa','đất','khí'],
                'correct_index' => 0,
                'explanation' => '水 (mizu) = nước',
            ],
            [
                'question' => '「赤」 là màu nào?',
                'choices' => ['đỏ','xanh lá','đen','trắng'],
                'correct_index' => 0,
                'explanation' => '赤 (aka) = đỏ',
            ],
        ];
        foreach ($mcqs as $i => $q) {
            $item = Item::create([
                'deck_id' => $vocab->id,
                'type' => ItemType::MCQ,
                'data' => $q, // {question, choices[4], correct_index, explanation}
            ]);
            ReviewState::create([
                'user_id' => $user->id,
                'item_id' => $item->id,
                'ease' => 2.36,
                'interval' => 1,
                'repetitions' => 1,
                'due_at' => now()->addHours($i+1), // một vài câu chưa đến hạn
                'last_reviewed_at' => now()->subDay(),
            ]);
        }

        // 5) MATCHING
        $pairs = [
            ['left' => 'one',   'right' => '一'],
            ['left' => 'two',   'right' => '二'],
            ['left' => 'three', 'right' => '三'],
        ];
        $item = Item::create([
            'deck_id' => $numbers->id,
            'type' => ItemType::MATCHING,
            'data' => ['pairs' => $pairs],
        ]);
        ReviewState::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
            'ease' => 2.5,
            'interval' => 0,
            'repetitions' => 0,
            'due_at' => now()->subHours(4),
        ]);
    }
}
