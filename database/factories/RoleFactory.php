<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class RoleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'role_name' => ['user', 'moderator', 'admin'],
            'access_level' => [1, 2, 3]
        ];
    }
}
