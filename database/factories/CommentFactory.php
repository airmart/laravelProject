<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CommentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'text' => $this->faker->text(),
            'user_id' => function() {
                return User::factory()->create()->id;
            },
            'post_id' => function() {
                return Post::factory()->create()->id;
            }
        ];
    }
}
