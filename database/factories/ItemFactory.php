<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Item;
use App\Models\Deck;
use Illuminate\Support\Carbon;

class ItemFactory extends Factory
{
    protected $model = Item::class;

    public function definition(): array
    {
        // Trộn due_at: 40% quá hạn, 30% hôm nay, 30% tương lai
        $r = rand(1, 10);
        if ($r <= 4) {
            $due = Carbon::now()->subDays(rand(1, 5));
        } elseif ($r <= 7) {
            $due = Carbon::now();
        } else {
            $due = Carbon::now()->addDays(rand(1, 7));
        }

        return [
            'deck_id'    => Deck::factory(),
            'type'       => 'flashcard',
            'front'      => ucfirst($this->faker->word()),
            'back'       => $this->faker->sentence(8),
            'data'       => null,
            'ef'         => 2.5,
            'interval'   => 1,
            'repetition' => 0,
            'due_at'     => $due,
            'created_at' => now()->subDays(rand(0, 10)),
            'updated_at' => now()->subDays(rand(0, 5)),
        ];
    }
}
