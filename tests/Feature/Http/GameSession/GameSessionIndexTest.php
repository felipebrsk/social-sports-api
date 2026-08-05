<?php

namespace Tests\Feature\Http\GameSession;

use Generator;
use Illuminate\Support\Carbon;
use App\Enums\GameSessionStatusEnum;
use Tests\Feature\BaseIntegrationTesting;
use App\Enums\GameSessionRequestStatusEnum;
use PHPUnit\Framework\Attributes\DataProvider;
use Database\Seeders\{
    GameSessionStatusSeeder,
    GameSessionRequestStatusSeeder,
};
use Tests\Traits\Dummy\{
    HasDummyTeam,
    HasDummySport,
    HasDummyVenue,
    HasDummySkillLevel,
    HasDummyGameSession,
    HasDummyGameSessionRequest,
};

class GameSessionIndexTest extends BaseIntegrationTesting
{
    use HasDummyTeam;
    use HasDummySport;
    use HasDummyVenue;
    use HasDummySkillLevel;
    use HasDummyGameSession;
    use HasDummyGameSessionRequest;

    /**
     * Get the route name.
     *
     * @return string
     */
    private function getRouteName(): string
    {
        return 'game-sessions.index';
    }

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
            GameSessionRequestStatusSeeder::class,
        ]);
    }

    /**
     * Test if unauthenticated user cannot access game sessions feed.
     *
     * @return void
     */
    public function test_if_unauthenticated_user_cannot_access_game_sessions_index(): void
    {
        $this->actingAsGuest();

        $this->getJson(route($this->getRouteName(), [
            'per_page' => 10,
        ]))->assertUnauthorized();
    }

    /**
     * Test validation rules for invalid query parameters using DataProvider.
     *
     * @param array<string, mixed> $payload
     * @param array<string, string> $expectedErrorMessages
     * @return void
     */
    #[DataProvider('invalidGameSessionQueryPayloadsProvider')]
    public function test_if_fails_with_invalid_query_parameters(
        array $payload,
        array $expectedErrorMessages,
    ): void {
        $response = $this->getJson(route($this->getRouteName(), $payload))
            ->assertUnprocessable()
            ->assertJsonValidationErrors(array_keys($expectedErrorMessages));

        foreach ($expectedErrorMessages as $message) {
            $response->assertSee($message);
        }
    }

    /**
     * Test listing game sessions with default required filters and json structure response.
     *
     * @return void
     */
    public function test_if_can_list_game_sessions_with_required_per_page_filter(): void
    {
        $now = Carbon::now();
        $venue = $this->createDummyVenue();
        $sport = $this->createDummySport();
        $skillLevel = $this->createDummySkillLevel();

        $gameSession = $this->createDummyGameSession([
            'venue_id' => $venue->id,
            'sport_id' => $sport->id,
            'skill_level_id' => $skillLevel->id,
            'creator_id' => $this->user->id,
            'max_players' => 10,
            'external_players_count' => 2,
            'description' => 'Partida Semanal',
            'start_time' => $now->copy()->addHours(2),
            'end_time' => $now->copy()->addHours(4),
            'game_session_status_id' => GameSessionStatusEnum::OPEN->value,
        ]);

        $this->getJson(route($this->getRouteName(), [
            'per_page' => 10,
        ]))->assertOk()->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'is_team_match',
                    'featured',
                    'description',
                    'end_time',
                    'start_time',
                    'distance_in_km',
                    'is_organizer',
                    'user_request_status',
                    'players' => [
                        'max',
                        'occupied',
                        'available',
                        'is_full',
                    ],
                    'venue' => [
                        'id',
                        'name',
                        'city',
                        'state',
                        'neighborhood',
                    ],
                    'sport' => [
                        'id',
                        'name',
                        'icon',
                    ],
                    'creator' => [
                        'id',
                        'name',
                    ],
                    'skill_level' => [
                        'id',
                        'name',
                    ],
                ],
            ],
        ])->assertJsonPath('data.0.id', $gameSession->id)
            ->assertJsonPath('data.0.description', 'Partida Semanal')
            ->assertJsonPath('data.0.is_organizer', true)
            ->assertJsonPath('data.0.players.max', 10)
            ->assertJsonPath('data.0.players.occupied', 2)
            ->assertJsonPath('data.0.players.available', 8)
            ->assertJsonPath('data.0.players.is_full', false);
    }

    /**
     * Test filtering game sessions by text search.
     *
     * @return void
     */
    public function test_if_can_filter_game_sessions_by_search_term(): void
    {
        $now = Carbon::now();

        $matchingGameSession = $this->createDummyGameSession([
            'description' => 'Pelada no Beach Club',
            'start_time' => $now->copy()->addHour(),
            'end_time' => $now->copy()->addHours(2),
            'game_session_status_id' => GameSessionStatusEnum::OPEN->value,
        ]);

        $otherGameSession = $this->createDummyGameSession([
            'description' => 'Futebol Society do Centro',
            'start_time' => $now->copy()->addHour(),
            'end_time' => $now->copy()->addHours(2),
            'game_session_status_id' => GameSessionStatusEnum::OPEN->value,
        ]);

        /** @var array<string, mixed> $data */
        $data = $this->getJson(route($this->getRouteName(), [
            'per_page' => 10,
            'search' => 'Beach',
        ]))->assertOk()->json('data');

        $ids = collect($data)->pluck('id')->toArray();

        $this->assertContains($matchingGameSession->id, $ids);
        $this->assertNotContains($otherGameSession->id, $ids);
    }

    /**
     * Test filtering game sessions by sport_id and skill_level_id.
     *
     * @return void
     */
    public function test_if_can_filter_game_sessions_by_sport_and_skill_level(): void
    {
        $now = Carbon::now();
        $sportA = $this->createDummySport();
        $sportB = $this->createDummySport();
        $skillLevelA = $this->createDummySkillLevel();

        $matchingGame = $this->createDummyGameSession([
            'sport_id' => $sportA->id,
            'skill_level_id' => $skillLevelA->id,
            'start_time' => $now->copy()->addHour(),
            'end_time' => $now->copy()->addHours(2),
            'game_session_status_id' => GameSessionStatusEnum::OPEN->value,
        ]);

        $otherGame = $this->createDummyGameSession([
            'sport_id' => $sportB->id,
            'skill_level_id' => $skillLevelA->id,
            'start_time' => $now->copy()->addHour(),
            'end_time' => $now->copy()->addHours(2),
            'game_session_status_id' => GameSessionStatusEnum::OPEN->value,
        ]);

        /** @var array<string, mixed> $data */
        $data = $this->getJson(route($this->getRouteName(), [
            'per_page' => 10,
            'sport_id' => $sportA->id,
            'skill_level_id' => $skillLevelA->id,
        ]))->assertOk()->json('data');

        $ids = collect($data)->pluck('id')->toArray();

        $this->assertContains($matchingGame->id, $ids);
        $this->assertNotContains($otherGame->id, $ids);
    }

    /**
     * Test distance calculation and distance_in_km inclusion when coordinates are supplied.
     *
     * @return void
     */
    public function test_if_includes_distance_in_km_when_coordinates_are_provided(): void
    {
        $userLat = -12.9714;
        $userLng = -38.5014;
        $now = Carbon::now();

        $nearVenue = $this->createDummyVenue([
            'latitude' => -12.9720,
            'longitude' => -38.5020,
        ]);

        $farVenue = $this->createDummyVenue([
            'latitude' => -10.0000,
            'longitude' => -30.0000,
        ]);

        $nearGame = $this->createDummyGameSession([
            'venue_id' => $nearVenue->id,
            'start_time' => $now->copy()->addHour(),
            'end_time' => $now->copy()->addHours(2),
            'game_session_status_id' => GameSessionStatusEnum::OPEN->value,
        ]);

        $farGame = $this->createDummyGameSession([
            'venue_id' => $farVenue->id,
            'start_time' => $now->copy()->addHour(),
            'end_time' => $now->copy()->addHours(2),
            'game_session_status_id' => GameSessionStatusEnum::OPEN->value,
        ]);

        /** @var array<int, array<string, mixed>> $data */
        $data = $this->getJson(route($this->getRouteName(), [
            'per_page' => 10,
            'latitude' => $userLat,
            'longitude' => $userLng,
            'radius_km' => 15,
        ]))->assertOk()->json('data');

        $ids = collect($data)->pluck('id')->toArray();

        $this->assertContains($nearGame->id, $ids);
        $this->assertNotContains($farGame->id, $ids);
        $this->assertNotNull($data[0]['distance_in_km']);
    }

    /**
     * Test correctly calculates full players availability state (is_full = true).
     *
     * @return void
     */
    public function test_if_correctly_identifies_full_game_session(): void
    {
        $now = Carbon::now();

        // 5 vagas: 3 solicitações aprovadas + 2 jogadores externos = 5 ocupadas
        $gameSession = $this->createDummyGameSession([
            'max_players' => 5,
            'external_players_count' => 2,
            'start_time' => $now->copy()->addHour(),
            'end_time' => $now->copy()->addHours(2),
            'game_session_status_id' => GameSessionStatusEnum::OPEN->value,
        ]);

        $this->createDummyGameSessionRequests(3, [
            'game_session_id' => $gameSession->id,
            'game_session_request_status_id' => GameSessionRequestStatusEnum::APPROVED->value,
        ]);

        $this->getJson(route($this->getRouteName(), [
            'per_page' => 10,
        ]))->assertOk()
            ->assertJsonPath('data.0.id', $gameSession->id)
            ->assertJsonPath('data.0.players.max', 5)
            ->assertJsonPath('data.0.players.occupied', 5)
            ->assertJsonPath('data.0.players.available', 0)
            ->assertJsonPath('data.0.players.is_full', true);
    }

    /**
     * Test includes user_request_status name when authenticated user requested to join.
     *
     * @return void
     */
    public function test_if_includes_user_request_status_when_authenticated_user_has_request(): void
    {
        $now = Carbon::now();

        $gameSession = $this->createDummyGameSession([
            'start_time' => $now->copy()->addHour(),
            'end_time' => $now->copy()->addHours(2),
            'game_session_status_id' => GameSessionStatusEnum::OPEN->value,
        ]);

        $this->createDummyGameSessionRequest([
            'user_id' => $this->user->id,
            'game_session_id' => $gameSession->id,
            'game_session_request_status_id' => GameSessionRequestStatusEnum::PENDING->value,
        ]);

        $this->getJson(route($this->getRouteName(), [
            'per_page' => 10,
        ]))->assertOk()
            ->assertJsonPath('data.0.id', $gameSession->id)
            ->assertJsonPath('data.0.is_organizer', false)
            ->assertJsonPath('data.0.user_request_status', 'Pendente');
    }

    /**
     * Test filtering game sessions by specific date.
     *
     * @return void
     */
    public function test_if_can_filter_game_sessions_by_date(): void
    {
        $targetDate = '2026-08-10';

        $matchingGame = $this->createDummyGameSession([
            'start_time' => Carbon::parse("{$targetDate} 19:00:00"),
            'end_time' => Carbon::parse("{$targetDate} 21:00:00"),
            'game_session_status_id' => GameSessionStatusEnum::OPEN->value,
        ]);

        $otherGame = $this->createDummyGameSession([
            'start_time' => Carbon::parse('2026-08-15 19:00:00'),
            'end_time' => Carbon::parse('2026-08-15 21:00:00'),
            'game_session_status_id' => GameSessionStatusEnum::OPEN->value,
        ]);

        /** @var array<int, array<string, mixed>> $data */
        $data = $this->getJson(route($this->getRouteName(), [
            'per_page' => 10,
            'date' => $targetDate,
        ]))->assertOk()->json('data');

        $ids = collect($data)->pluck('id')->toArray();

        $this->assertContains($matchingGame->id, $ids);
        $this->assertNotContains($otherGame->id, $ids);
    }

    /**
     * Data provider with generators for invalid game session filter payloads.
     *
     * @return Generator<string, array{payload: array<string, mixed>, expectedErrorMessages: array<string, string>}>
     */
    public static function invalidGameSessionQueryPayloadsProvider(): Generator
    {
        yield 'missing required per_page field' => [
            'payload' => [],
            'expectedErrorMessages' => [
                'per_page' => 'O campo por p\u00e1gina \u00e9 obrigat\u00f3rio.',
            ],
        ];

        yield 'invalid numeric data types' => [
            'payload' => [
                'per_page' => 'abc',
                'sport_id' => 'not-a-number',
                'venue_id' => 'not-a-number',
                'skill_level_id' => 'not-a-number',
                'radius_km' => 'abc',
                'latitude' => 'invalid-lat',
                'longitude' => 'invalid-lng',
            ],
            'expectedErrorMessages' => [
                'per_page' => 'O campo por p\u00e1gina deve ser um n\u00famero.',
                'sport_id' => 'O campo esporte deve ser um n\u00famero.',
                'venue_id' => 'O campo quadra deve ser um n\u00famero.',
                'skill_level_id' => 'O campo n\u00edvel de habilidade deve ser um n\u00famero.',
                'radius_km' => 'O campo raio em km deve ser um n\u00famero.',
                'latitude' => 'O campo latitude deve ser um n\u00famero.',
                'longitude' => 'O campo longitude deve ser um n\u00famero.',
            ],
        ];

        yield 'per_page boundary below minimum' => [
            'payload' => [
                'per_page' => 0,
            ],
            'expectedErrorMessages' => [
                'per_page' => 'O campo por p\u00e1gina deve ser entre 1 e 50.',
            ],
        ];

        yield 'per_page boundary above maximum' => [
            'payload' => [
                'per_page' => 51,
            ],
            'expectedErrorMessages' => [
                'per_page' => 'O campo por p\u00e1gina deve ser entre 1 e 50.',
            ],
        ];

        yield 'radius_km boundary values' => [
            'payload' => [
                'per_page' => 10,
                'radius_km' => 51,
            ],
            'expectedErrorMessages' => [
                'radius_km' => 'O campo raio em km deve ser entre 1 e 50.',
            ],
        ];

        yield 'invalid date format' => [
            'payload' => [
                'per_page' => 10,
                'date' => '03-08-2026',
            ],
            'expectedErrorMessages' => [
                'date' => 'O campo data n\u00e3o corresponde ao formato Y-m-d.',
            ],
        ];

        yield 'state code invalid size' => [
            'payload' => [
                'per_page' => 10,
                'state' => 'BAH',
            ],
            'expectedErrorMessages' => [
                'state' => 'O campo estado deve ser 2 caracteres.',
            ],
        ];

        yield 'coordinates exceeding negative and positive ranges' => [
            'payload' => [
                'per_page' => 10,
                'latitude' => 95.0000,
                'longitude' => -185.0000,
            ],
            'expectedErrorMessages' => [
                'latitude' => 'O campo latitude deve ser entre -90 e 90.',
                'longitude' => 'O campo longitude deve ser entre -180 e 180.',
            ],
        ];

        yield 'invalid sort_order value' => [
            'payload' => [
                'per_page' => 10,
                'sort_order' => 'unsupported_order',
            ],
            'expectedErrorMessages' => [
                'sort_order' => 'O campo ordem selecionado \u00e9 inv\u00e1lido.',
            ],
        ];
    }
}
