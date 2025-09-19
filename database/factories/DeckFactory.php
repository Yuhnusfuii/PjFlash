<?php

namespace Database\Factories;

use App\Models\Deck;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class DeckFactory extends Factory
{
    protected $model = Deck::class;

    public function definition(): array
    {
        return [
            'user_id'    => User::factory(),           // cho phép override trong test
            'name'       => $this->faker->words(3, true),
            'description'=> $this->faker->sentence(),
            'parent_id'  => null,                      // sub-deck => có thể thêm state later
            // nếu bảng của bạn có cột khác thì thêm vào đây
        ];
    }
}
