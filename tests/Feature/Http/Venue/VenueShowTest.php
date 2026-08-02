<?php

namespace Tests\Feature\Http\Venue;

use Generator;
use Illuminate\Support\Carbon;
use App\Enums\GameSessionStatusEnum;
use Tests\Feature\BaseIntegrationTesting;
use Illuminate\Database\Eloquent\Collection;
use Database\Seeders\GameSessionStatusSeeder;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Traits\Dummy\{
    HasDummySport,
    HasDummyVenue,
    HasDummyProfile,
    HasDummyGameSession,
};
use App\Models\{
    User,
    Sport,
    Venue,
    Profile,
    SkillLevel,
    GameSession,
};

class VenueShowTest extends BaseIntegrationTesting
{
    use HasDummySport;
    use HasDummyVenue;
    use HasDummyProfile;
    use HasDummyGameSession;

    /**
     * The dummy venue.
     *
     * @var Venue
     */
    private Venue $venue;

    /**
     * Get the route name.
     *
     * @return string
     */
    private function getRouteName(): string
    {
        return 'venues.show';
    }

    /**
     * Setup test environments.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            GameSessionStatusSeeder::class,
        ]);

        $this->venue = $this->createDummyVenue();
    }

    /**
     * Test if unauthenticated user cannot access venue details.
     *
     * @return void
     */
    public function test_if_unauthenticated_user_cannot_access_venue_show(): void
    {
        $this->actingAsGuest();

        $this->getJson(route($this->getRouteName(), [
            'venue' => $this->venue->id,
        ]))->assertUnauthorized();
    }

    /**
     * Test if returns 404 when venue is not found.
     *
     * @return void
     */
    public function test_if_returns_not_found_when_venue_does_not_exist(): void
    {
        $nonExistingId = 999999;

        $this->getJson(route($this->getRouteName(), [
            'venue' => $nonExistingId,
        ]))->assertNotFound();
    }

    /**
     * Test if can get correct venue show json attributes count.
     *
     * @return void
     */
    public function test_if_can_get_correct_venue_show_json_attributes_count(): void
    {
        $this->getJson(route($this->getRouteName(), [
            'venue' => $this->venue->id,
        ]))->assertOk()->assertJsonCount(13, 'data');
    }

    /**
     * Test if can get correct venue show json structure.
     *
     * @return void
     */
    public function test_if_can_get_correct_venue_show_json_structure(): void
    {
        $sport = $this->createDummySport(['name' => 'Vôlei de Quadra', 'icon' => 'volleyball']);

        $this->venue->sports()->attach($sport->id);

        $now = Carbon::now();

        $creator = $this->createDummyUser();

        $this->createDummyProfileTo($creator->id);

        $this->createDummyGameSession([
            'sport_id' => $sport->id,
            'creator_id' => $creator->id,
            'venue_id' => $this->venue->id,
            'description' => 'Jogo de Vôlei',
            'start_time' => $now->copy()->addHour(),
            'end_time' => $now->copy()->addHours(3),
            'game_session_status_id' => GameSessionStatusEnum::OPEN->value ?? 1,
        ]);

        $this->getJson(route($this->getRouteName(), [
            'venue' => $this->venue->id,
        ]))->assertOk()->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'city',
                'state',
                'address',
                'latitude',
                'longitude',
                'verified',
                'featured',
                'neighborhood',
                'sports' => [
                    '*' => [
                        'id',
                        'name',
                        'icon',
                    ],
                ],
                'game_sessions' => [
                    '*' => [
                        'id',
                        'start_time',
                        'end_time',
                        'description',
                        'max_players',
                        'external_players_count',
                        'sport' => [
                            'id',
                            'name',
                            'icon',
                        ],
                        'creator' => [
                            'id',
                            'name',
                            'profile' => [
                                'avatar',
                            ],
                        ],
                        'skill_level' => [
                            'id',
                            'name',
                        ],
                    ],
                ],
            ],
        ]);
    }

    /**
     * Test if can get correct venue show json data.
     *
     * @return void
     */
    public function test_if_can_get_correct_venue_show_json_data(): void
    {
        $sport = $this->createDummySport(['name' => 'Vôlei de Quadra', 'icon' => 'volleyball']);

        $this->venue->sports()->attach($sport->id);

        $now = Carbon::now();

        $creator = $this->createDummyUser();

        $this->createDummyProfileTo($creator->id);

        $this->createDummyGameSession([
            'sport_id' => $sport->id,
            'creator_id' => $creator->id,
            'venue_id' => $this->venue->id,
            'description' => 'Jogo de Vôlei',
            'start_time' => $now->copy()->addHour(),
            'end_time' => $now->copy()->addHours(3),
            'game_session_status_id' => GameSessionStatusEnum::OPEN->value ?? 1,
        ]);

        /** @var Collection<int, Sport> $sports */
        $sports = $this->venue->sports;

        /** @var Collection<int, GameSession> $games */
        $games = $this->venue->gameSessions;

        $this->getJson(route($this->getRouteName(), [
            'venue' => $this->venue->id,
        ]))->assertOk()->assertJson([
            'data' => [
                'id' => $this->venue->id,
                'name' => $this->venue->name,
                'city' => $this->venue->city,
                'state' => $this->venue->state,
                'address' => $this->venue->address,
                'latitude' => $this->venue->latitude,
                'longitude' => $this->venue->longitude,
                'verified' => $this->venue->verified,
                'featured' => $this->venue->featured,
                'neighborhood' => $this->venue->neighborhood,
                'sports' => $sports->map(function (Sport $sport) {
                    return [
                        'id' => $sport->id,
                        'name' => $sport->name,
                        'icon' => $sport->icon,
                    ];
                })->toArray(),
                /** @phpstan-ignore-next-line */
                'game_sessions' => $games->map(function (GameSession $game) {
                    /** @var Sport $sport */
                    $sport = $game->sport;

                    /** @var User $creator */
                    $creator = $game->creator;

                    /** @var Profile $profile */
                    $profile = $creator->profile;

                    /** @var SkillLevel $skillLevel */
                    $skillLevel = $game->skillLevel;

                    return [
                        'id' => $game->id,
                        /** @phpstan-ignore-next-line */
                        'end_time' => $game->end_time?->toISOString(),
                        /** @phpstan-ignore-next-line */
                        'start_time' => $game->start_time->toISOString(),
                        'description' => $game->description,
                        'max_players' => $game->max_players,
                        'external_players_count' => $game->external_players_count,
                        'sport' => [
                            'id' => $sport->id,
                            'name' => $sport->name,
                            'icon' => $sport->icon,
                        ],
                        'creator' => [
                            'id' => $creator->id,
                            'name' => $creator->name,
                            'profile' => [
                                'avatar' => $profile->avatar,
                            ],
                        ],
                        'skill_level' => [
                            'id' => $skillLevel->id,
                            'name' => $skillLevel->name,
                        ],
                    ];
                })->toArray(),
            ],
        ]);
    }

    /**
     * Test distance calculation when coordinates are provided.
     *
     * @return void
     */
    public function test_if_includes_distance_in_km_when_coordinates_are_provided(): void
    {
        $venue = $this->createDummyVenue([
            'latitude' => -10.83839578,
            'longitude' => -38.54361091,
        ]);

        $userLat = -10.83500000;
        $userLng = -38.54000000;

        $this->getJson(route($this->getRouteName(), [
            'venue' => $venue->id,
            'latitude' => $userLat,
            'longitude' => $userLng,
        ]))->assertOk()->assertJsonPath('data.distance_in_km', 0.5459784453002623);
    }

    /**
     * Test if ignores finished and cancelled game sessions in details response.
     *
     * @return void
     */
    public function test_if_ignores_finished_and_cancelled_game_sessions(): void
    {
        $now = Carbon::now();

        // Valid (Upcoming)
        $validSession = $this->createDummyGameSession([
            'venue_id' => $this->venue->id,
            'start_time' => $now->copy()->addHour(),
            'end_time' => $now->copy()->addHours(2),
            'game_session_status_id' => GameSessionStatusEnum::OPEN->value ?? 1,
        ]);

        // Cancelled (Ignored)
        $this->createDummyGameSession([
            'venue_id' => $this->venue->id,
            'start_time' => $now->copy()->addHour(),
            'end_time' => $now->copy()->addHours(2),
            'game_session_status_id' => GameSessionStatusEnum::CANCELLED->value,
        ]);

        // Finished by status (Ignored)
        $this->createDummyGameSession([
            'venue_id' => $this->venue->id,
            'start_time' => $now->copy()->subHours(3),
            'end_time' => $now->copy()->addHour(),
            'game_session_status_id' => GameSessionStatusEnum::FINISHED->value,
        ]);

        // Expired end_time (Ignored)
        $this->createDummyGameSession([
            'venue_id' => $this->venue->id,
            'start_time' => $now->copy()->subHours(3),
            'end_time' => $now->copy()->subMinutes(10),
            'game_session_status_id' => GameSessionStatusEnum::OPEN->value ?? 1,
        ]);

        $this->getJson(route($this->getRouteName(), [
            'venue' => $this->venue->id,
        ]))->assertOk()
            ->assertJsonCount(1, 'data.game_sessions')
            ->assertJsonPath('data.game_sessions.0.id', $validSession->id);
    }

    /**
     * Test validation rules for invalid venue show query parameters using DataProvider.
     *
     * @param array<string, mixed> $payload
     * @param array<string, string> $expectedErrorMessages
     * @return void
     */
    #[DataProvider('invalidVenueShowQueryPayloadsProvider')]
    public function test_if_fails_with_invalid_query_parameters(
        array $payload,
        array $expectedErrorMessages,
    ): void {
        $response = $this->getJson(route($this->getRouteName(), [
            'venue' => $this->venue->id,
            ...$payload,
        ]))->assertUnprocessable()->assertJsonValidationErrors(array_keys($expectedErrorMessages));

        foreach ($expectedErrorMessages as $message) {
            $response->assertSee($message);
        }
    }

    /**
     * Data provider with generators for invalid venue show query payloads.
     *
     * @return Generator<string, array{payload: array<string, mixed>, expectedErrorMessages: array<string, string>}>
     */
    public static function invalidVenueShowQueryPayloadsProvider(): Generator
    {
        yield 'invalid latitude and longitude types' => [
            'payload' => [
                'latitude' => 'invalid-latitude',
                'longitude' => 'invalid-longitude',
            ],
            'expectedErrorMessages' => [
                'latitude' => 'O campo latitude deve ser um n\u00famero.',
                'longitude' => 'O campo longitude deve ser um n\u00famero.',
            ],
        ];

        yield 'coordinates exceeding allowed boundaries' => [
            'payload' => [
                'latitude' => 95.0000,
                'longitude' => -185.0000,
            ],
            'expectedErrorMessages' => [
                'latitude' => 'O campo latitude deve ser entre -90 e 90.',
                'longitude' => 'O campo longitude deve ser entre -180 e 180.',
            ],
        ];
    }
}
