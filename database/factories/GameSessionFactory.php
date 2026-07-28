<?php

namespace Database\Factories;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\{
    User,
    Team,
    Sport,
    Venue,
    SkillLevel,
    GameSession,
    GameSessionStatus,
};

/**
 * @extends Factory<GameSession>
 */
class GameSessionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'featured' => fake()->boolean(),
            'description' => fake()->sentence(),
            'end_time' => Carbon::now()->addHour(),
            'start_time' => Carbon::now()->subHour(),
            'max_players' => fake()->numberBetween(1, 60),
            'external_players_count' => fake()->numberBetween(1, 60),
            'sport_id' => Sport::factory(),
            'venue_id' => Venue::factory(),
            'creator_id' => User::factory(),
            'host_team_id' => Team::factory(),
            'visitor_team_id' => Team::factory(),
            'skill_level_id' => SkillLevel::factory(),
            'game_session_status_id' => GameSessionStatus::factory(),
        ];
    }
}
