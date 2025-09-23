<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Deck;
use App\Models\User;

class DeckFactory extends Factory
{
    protected $model = Deck::class;

    public function definition(): array
    {
        return [
            'user_id'     => User::factory(),
            'name'        => $this->faker->words(3, true),
            'description' => $this->faker->boolean(40) ? $this->faker->sentence(10) : null,
            'created_at'  => now()->subDays(rand(0, 20)),
            'updated_at'  => now()->subDays(rand(0, 10)),
        ];
    }
}
