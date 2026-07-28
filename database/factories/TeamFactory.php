<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\{
    User,
    Team,
    Sport,
};

/**
 * @extends Factory<Team>
 */
class TeamFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'logo' => fake()->imageUrl(),
            'description' => fake()->sentence(),
            'sport_id' => Sport::factory(),
            'leader_id' => User::factory(),
        ];
    }
}
