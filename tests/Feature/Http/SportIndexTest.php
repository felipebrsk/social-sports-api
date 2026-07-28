<?php

namespace Tests\Feature\Http;

use App\Models\Sport;
use Illuminate\Support\Carbon;
use App\Enums\GameSessionStatusEnum;
use Tests\Feature\BaseIntegrationTesting;
use Database\Seeders\GameSessionStatusSeeder;
use Tests\Traits\Dummy\{
    HasDummySport,
    HasDummyVenue,
    HasDummyGameSession,
};

class SportIndexTest extends BaseIntegrationTesting
{
    use HasDummySport;
    use HasDummyVenue;
    use HasDummyGameSession;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            GameSessionStatusSeeder::class,
        ]);
    }

    /**
     * Get the route name.
     *
     * @return string
     */
    private function getRouteName(): string
    {
        return 'sports.index';
    }

    /**
     * Test if route can return ok.
     *
     * @return void
     */
    public function test_if_route_can_return_ok(): void
    {
        $this->getJson(route($this->getRouteName()))->assertOk();
    }

    /**
     * Test if can return correct sports json count.
     *
     * @return void
     */
    public function test_if_can_return_correct_sports_json_count(): void
    {
        $this->getJson(route($this->getRouteName()))->assertOk()->assertJsonCount(0, 'data');

        $this->createDummySports(2);

        $this->getJson(route($this->getRouteName()))->assertOk()->assertJsonCount(2, 'data');
    }

    /**
     * Test if can get correct sports json structure.
     *
     * @return void
     */
    public function test_if_can_get_correct_sports_json_structure(): void
    {
        $this->createDummySport();

        $this->getJson(route($this->getRouteName()))->assertOk()->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'icon',
                    'venues_count',
                    'ongoing_games_count',
                    'upcoming_games_count',
                ],
            ],
        ]);
    }

    /**
     * Test if can get correct sports json data.
     *
     * @return void
     */
    public function test_if_can_get_correct_sports_json_data(): void
    {
        $sports = $this->createDummySports(2);

        $this->getJson(route($this->getRouteName()))->assertOk()->assertJson([
            'data' => $sports->map(function (Sport $sport) {
                return [
                    'id' => $sport->id,
                    'name' => $sport->name,
                    'icon' => $sport->icon,
                    'venues_count' => 0,
                    'ongoing_games_count' => 0,
                    'upcoming_games_count' => 0,
                ];
            })->toArray(),
        ]);
    }

    /**
     * Test if can get all sports with correct structure and counters.
     *
     * @return void
     */
    public function test_if_can_get_all_sports_with_correct_counters(): void
    {
        $sportEmpty = $this->createDummySport();
        $sportWithGames = $this->createDummySport();

        $venue1 = $this->createDummyVenue();
        $venue2 = $this->createDummyVenue();

        $sportWithGames->venues()->attach([
            $venue1->id,
            $venue2->id,
        ]);

        // Ongoing
        $this->createDummyGameSession([
            'sport_id' => $sportWithGames->id,
            'end_time' => Carbon::now()->addHour(),
            'start_time' => Carbon::now()->subHour(),
            'game_session_status_id' => GameSessionStatusEnum::OPEN->value,
        ]);

        // Upcoming
        $this->createDummyGameSession([
            'sport_id' => $sportWithGames->id,
            'start_time' => Carbon::now()->addHour(),
            'end_time' => Carbon::now()->addHours(2),
            'game_session_status_id' => GameSessionStatusEnum::OPEN->value,
        ]);

        // Upcoming but CANCELLED
        $this->createDummyGameSession([
            'sport_id' => $sportWithGames->id,
            'start_time' => Carbon::now()->addHours(2),
            'end_time' => Carbon::now()->addHours(4),
            'game_session_status_id' => GameSessionStatusEnum::CANCELLED->value,
        ]);

        // Ongoing but FINISHED
        $this->createDummyGameSession([
            'sport_id' => $sportWithGames->id,
            'end_time' => Carbon::now()->addHour(),
            'start_time' => Carbon::now()->subHour(),
            'game_session_status_id' => GameSessionStatusEnum::FINISHED->value,
        ]);

        // Past game
        $this->createDummyGameSession([
            'sport_id' => $sportWithGames->id,
            'start_time' => Carbon::now()->subHour(),
            'end_time' => Carbon::now()->subHours(2),
            'game_session_status_id' => GameSessionStatusEnum::OPEN->value,
        ]);

        $this->getJson(route($this->getRouteName()))
            ->assertOk()
            ->assertJsonFragment([
                'id' => $sportWithGames->id,
                'name' => $sportWithGames->name,
                'venues_count' => 2,
                'upcoming_games_count' => 1, // Upcoming OPEN
                'ongoing_games_count' => 1,  // Ongoing OPEN
            ])->assertJsonFragment([
                'id' => $sportEmpty->id,
                'name' => $sportEmpty->name,
                'venues_count' => 0,
                'upcoming_games_count' => 0,
                'ongoing_games_count' => 0,
            ]);
    }
}
