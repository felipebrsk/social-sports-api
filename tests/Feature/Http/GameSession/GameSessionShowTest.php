<?php

namespace Tests\Feature\Http\GameSession;

use Generator;
use App\Models\GameSession;
use Illuminate\Support\Carbon;
use Tests\Feature\BaseIntegrationTesting;
use PHPUnit\Framework\Attributes\DataProvider;
use App\Enums\{
    GameSessionStatusEnum,
    GameSessionRequestStatusEnum,
};
use Database\Seeders\{
    SocialNetworkSeeder,
    GameSessionStatusSeeder,
    GameSessionRequestStatusSeeder,
};
use Tests\Traits\Dummy\{
    HasDummyTeam,
    HasDummySport,
    HasDummyVenue,
    HasDummyProfile,
    HasDummySocialLink,
    HasDummySkillLevel,
    HasDummyGameSession,
    HasDummyGameSessionRequest,
};

class GameSessionShowTest extends BaseIntegrationTesting
{
    use HasDummyTeam;
    use HasDummySport;
    use HasDummyVenue;
    use HasDummyProfile;
    use HasDummySkillLevel;
    use HasDummySocialLink;
    use HasDummyGameSession;
    use HasDummyGameSessionRequest;

    /**
     * The dummy game session.
     *
     * @var GameSession
     */
    private GameSession $gameSession;

    /**
     * Get the route name.
     *
     * @return string
     */
    private function getRouteName(): string
    {
        return 'game-sessions.show';
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
            SocialNetworkSeeder::class,
            GameSessionStatusSeeder::class,
            GameSessionRequestStatusSeeder::class,
        ]);

        $this->gameSession = $this->createDummyGameSession();
    }

    /**
     * Test if unauthenticated user cannot access game session details.
     *
     * @return void
     */
    public function test_if_unauthenticated_user_cannot_access_game_session_show(): void
    {
        $this->actingAsGuest();

        $this->getJson(route($this->getRouteName(), [
            'game_session' => 1,
        ]))->assertUnauthorized();
    }

    /**
     * Test if returns 404 when game session does not exist.
     *
     * @return void
     */
    public function test_if_returns_404_when_game_session_not_found(): void
    {
        $nonExistentId = 999999;

        $this->getJson(route($this->getRouteName(), [
            'game_session' => $nonExistentId,
        ]))->assertNotFound();
    }

    /**
     * Test validation rules for invalid query parameters using DataProvider.
     *
     * @param array<string, mixed> $payload
     * @param array<string, string> $expectedErrorMessages
     * @return void
     */
    #[DataProvider('invalidGameSessionShowQueryPayloadsProvider')]
    public function test_if_fails_with_invalid_query_parameters(
        array $payload,
        array $expectedErrorMessages,
    ): void {
        $response = $this->getJson(route($this->getRouteName(), [
            'game_session' => $this->gameSession->id,
            ...$payload,
        ]))->assertUnprocessable()->assertJsonValidationErrors(array_keys($expectedErrorMessages));

        foreach ($expectedErrorMessages as $message) {
            $response->assertSee($message);
        }
    }

    /**
     * Test if can get correct game session json data count.
     *
     * @return void
     */
    public function test_if_can_get_correct_game_session_json_data_count(): void
    {
        $this->getJson(route($this->getRouteName(), [
            'game_session' => $this->gameSession->id,
        ]))->assertOk()->assertJsonCount(18, 'data');
    }

    /**
     * Test if can get correct game session json data structure.
     *
     * @return void
     */
    public function test_if_can_get_correct_game_session_json_data_structure(): void
    {
        $creator = $this->createDummyUser();

        $this->createDummyProfileTo($creator->id);

        $this->gameSession->update([
            'creator_id' => $creator->id,
        ]);

        $requester = $this->createDummyUser();

        $this->createDummyProfileTo($requester->id);

        $this->createDummyGameSessionRequest([
            'user_id' => $requester->id,
            'game_session_id' => $this->gameSession->id,
            'game_session_request_status_id' => GameSessionRequestStatusEnum::APPROVED->value,
        ]);

        $this->createDummySocialLink([
            'linkable_id' => $this->gameSession->id,
            'linkable_type' => $this->gameSession::class,
        ]);

        $this->getJson(route($this->getRouteName(), [
            'game_session' => $this->gameSession->id,
        ]))->assertOk()->assertJsonStructure([
            'data' => [
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
                    'external_players_count',
                ],
                'teams' => [
                    'host' => [
                        'id',
                        'name',
                        'logo',
                    ],
                    'visitor' => [
                        'id',
                        'name',
                        'logo',
                    ],
                ],
                'sport' => [
                    'id',
                    'name',
                    'icon',
                ],
                'status' => [
                    'id',
                    'name',
                ],
                'creator' => [
                    'id',
                    'name',
                    'profile' => [
                        'avatar',
                    ],
                ],
                'venue' => [
                    'id',
                    'name',
                    'city',
                    'state',
                    'latitude',
                    'verified',
                    'featured',
                    'longitude',
                    'neighborhood',
                ],
                'skill_level' => [
                    'id',
                    'name',
                ],
                'social_links' => [
                    '*' => [
                        'id',
                        'url',
                        'network' => [
                            'id',
                            'name',
                            'icon',
                        ],
                    ],
                ],
                'approved_requests' => [
                    '*' => [
                        'id',
                        'user' => [
                            'id',
                            'name',
                            'profile' => [
                                'avatar',
                            ],
                        ],
                    ],
                ],
            ],
        ]);
    }

    /**
     * Test fetching game session details successfully with JSON structure and relationships.
     *
     * @return void
     */
    public function test_if_can_get_game_session_details(): void
    {
        $now = Carbon::now();

        $venue = $this->createDummyVenue([
            'latitude' => -12.9720,
            'longitude' => -38.5020,
        ]);

        $sport = $this->createDummySport();
        $skillLevel = $this->createDummySkillLevel();
        $hostTeam = $this->createDummyTeam();
        $visitorTeam = $this->createDummyTeam();

        $gameSession = $this->createDummyGameSession([
            'venue_id' => $venue->id,
            'sport_id' => $sport->id,
            'skill_level_id' => $skillLevel->id,
            'creator_id' => $this->user->id,
            'host_team_id' => $hostTeam->id,
            'visitor_team_id' => $visitorTeam->id,
            'max_players' => 10,
            'external_players_count' => 2,
            'description' => 'Detalhes da Partida',
            'start_time' => $now->copy()->addHours(2),
            'end_time' => $now->copy()->addHours(4),
            'game_session_status_id' => GameSessionStatusEnum::OPEN->value,
        ]);

        $this->getJson(route($this->getRouteName(), [
            'game_session' => $gameSession->id,
        ]))->assertOk()
            ->assertJsonPath('data.id', $gameSession->id)
            ->assertJsonPath('data.description', 'Detalhes da Partida')
            ->assertJsonPath('data.is_organizer', true)
            ->assertJsonPath('data.players.max', 10)
            ->assertJsonPath('data.players.occupied', 2)
            ->assertJsonPath('data.players.available', 8)
            ->assertJsonPath('data.players.is_full', false);
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

        $venue = $this->createDummyVenue([
            'latitude' => -12.9720,
            'longitude' => -38.5020,
        ]);

        $gameSession = $this->createDummyGameSession([
            'venue_id' => $venue->id,
        ]);

        $response = $this->getJson(route($this->getRouteName(), [
            'game_session' => $gameSession->id,
            'latitude' => $userLat,
            'longitude' => $userLng,
        ]))->assertOk();

        $this->assertNotNull($response->json('data.distance_in_km'));
    }

    /**
     * Test includes user_request_status and approved requests list when authenticated user has requested.
     *
     * @return void
     */
    public function test_if_includes_user_request_status_and_approved_requests(): void
    {
        $gameSession = $this->createDummyGameSession([
            'max_players' => 10,
            'external_players_count' => 1,
        ]);

        $this->createDummyGameSessionRequest([
            'user_id' => $this->user->id,
            'game_session_id' => $gameSession->id,
            'game_session_request_status_id' => GameSessionRequestStatusEnum::PENDING->value,
        ]);

        $approvedRequest = $this->createDummyGameSessionRequest([
            'game_session_id' => $gameSession->id,
            'game_session_request_status_id' => GameSessionRequestStatusEnum::APPROVED->value,
        ]);

        $this->getJson(route($this->getRouteName(), ['game_session' => $gameSession->id]))
            ->assertOk()
            ->assertJsonPath('data.id', $gameSession->id)
            ->assertJsonPath('data.is_organizer', false)
            ->assertJsonPath('data.user_request_status', 'Pendente')
            ->assertJsonPath('data.players.occupied', 2) // 1 externa + 1 aprovada
            ->assertJsonPath('data.approved_requests.0.id', $approvedRequest->id);
    }

    /**
     * Data provider with generators for invalid game session show query payloads.
     *
     * @return Generator<string, array{payload: array<string, mixed>, expectedErrorMessages: array<string, string>}>
     */
    public static function invalidGameSessionShowQueryPayloadsProvider(): Generator
    {
        yield 'invalid numeric coordinate types' => [
            'payload' => [
                'latitude' => 'invalid-lat',
                'longitude' => 'invalid-lng',
            ],
            'expectedErrorMessages' => [
                'latitude' => 'O campo latitude deve ser um n\u00famero.',
                'longitude' => 'O campo longitude deve ser um n\u00famero.',
            ],
        ];

        yield 'coordinates exceeding negative and positive ranges' => [
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
