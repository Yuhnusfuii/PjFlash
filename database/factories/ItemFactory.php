<?php

namespace Database\Factories;

use App\Models\Item;
use App\Models\Deck;
use Illuminate\Database\Eloquent\Factories\Factory;

class ItemFactory extends Factory
{
    protected $model = Item::class;

    public function definition(): array
    {
        return [
            'deck_id' => Deck::factory(),
            'type'    => 'flashcard', // hoặc enum->value nếu bạn dùng Enum caster
            'front'   => $this->faker->word(),
            'back'    => $this->faker->word(),
            'data'    => null,        // JSON cho MCQ/matching
        ];
    }
}
