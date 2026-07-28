<?php

namespace Database\Factories;

use App\Models\Venue;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Venue>
 */
class VenueFactory extends Factory
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
            'city' => fake()->city(),
            'verified' => fake()->boolean(),
            'latitude' => fake()->latitude(),
            'neighborhood' => fake()->word(),
            'longitude' => fake()->longitude(),
            'address' => fake()->streetAddress(),
            'state' => fake()->randomElement(['BA', 'MG', 'SP', 'RJ']),
        ];
    }
}
