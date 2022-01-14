<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->title(),
            'text' => $this->faker->text(),
            'user_id' => function() {
                return User::factory()->create();
            },
        ];
    }
}
