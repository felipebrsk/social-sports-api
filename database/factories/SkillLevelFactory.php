<?php

namespace Database\Factories;

use App\Models\SkillLevel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SkillLevel>
 */
class SkillLevelFactory extends Factory
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
        ];
    }
}
