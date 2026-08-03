<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\{
    GameSession,
    User,
    GameSessionRequest,
    GameSessionRequestStatus,
};

/**
 * @extends Factory<GameSessionRequest>
 */
class GameSessionRequestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'game_session_id' => GameSession::factory(),
            'game_session_request_status_id' => GameSessionRequestStatus::factory(),
        ];
    }
}
