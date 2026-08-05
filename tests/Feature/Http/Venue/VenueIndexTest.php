<?php

namespace Tests\Feature\Http\Venue;

use Generator;
use Illuminate\Support\Carbon;
use App\Enums\GameSessionStatusEnum;
use Tests\Feature\BaseIntegrationTesting;
use Database\Seeders\GameSessionStatusSeeder;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Traits\Dummy\{
    HasDummySport,
    HasDummyVenue,
    HasDummyGameSession,
};

class VenueIndexTest extends BaseIntegrationTesting
{
    use HasDummySport;
    use HasDummyVenue;
    use HasDummyGameSession;

    /**
     * Get the route name.
     *
     * @return string
     */
    private function getRouteName(): string
    {
        return 'venues.index';
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
        ]);
    }

    /**
     * Test if unauthenticated user cannot access venues list.
     *
     * @return void
     */
    public function test_if_unauthenticated_user_cannot_access_venues_index(): void
    {
        $this->actingAsGuest();

        $this->getJson(route($this->getRouteName(), [
            'per_page' => 10,
        ]))->assertUnauthorized();
    }

    /**
     * Test validation rules for invalid venue query parameters using DataProvider.
     *
     * @param array<string, mixed> $payload
     * @param array<string, string> $expectedErrorMessages
     * @return void
     */
    #[DataProvider('invalidVenueQueryPayloadsProvider')]
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
     * Test listing venues with default required filters and json structure response.
     *
     * @return void
     */
    public function test_if_can_list_venues_with_required_per_page_filter(): void
    {
        $venue = $this->createDummyVenue([
            'name' => 'Arena Central',
            'verified' => true,
            'featured' => true,
            'neighborhood' => 'Pituba',
        ]);

        $this->getJson(route($this->getRouteName(), [
            'per_page' => 10,
        ]))->assertOk()->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'verified',
                    'featured',
                    'neighborhood',
                    'game_sessions_count',
                    'ongoing_games_count',
                    'upcoming_games_count',
                ],
            ],
        ])->assertJsonFragment([
            'id' => $venue->id,
            'name' => 'Arena Central',
            'neighborhood' => 'Pituba',
        ]);
    }

    /**
     * Test filtering venues by text search.
     *
     * @return void
     */
    public function test_if_can_filter_venues_by_search_term(): void
    {
        $matchingVenue = $this->createDummyVenue(['name' => 'Arena Pituba Beach']);
        $otherVenue = $this->createDummyVenue(['name' => 'Quadra do Centro']);

        $this->getJson(route($this->getRouteName(), [
            'per_page' => 10,
            'search' => 'Pituba',
        ]))->assertOk()->assertJsonFragment([
            'id' => $matchingVenue->id,
        ])->assertJsonMissing([
            'id' => $otherVenue->id,
        ]);
    }

    /**
     * Test filtering venues by sport relationship.
     *
     * @return void
     */
    public function test_if_can_filter_venues_by_sport_id(): void
    {
        $sportA = $this->createDummySport();
        $sportB = $this->createDummySport();

        $venueWithSportA = $this->createDummyVenue();
        $venueWithSportA->sports()->attach($sportA->id);

        $venueWithSportB = $this->createDummyVenue();
        $venueWithSportB->sports()->attach($sportB->id);

        $this->getJson(route($this->getRouteName(), [
            'per_page' => 10,
            'sport_id' => $sportA->id,
        ]))->assertOk()->assertJsonFragment([
            'id' => $venueWithSportA->id,
        ])->assertJsonMissing([
            'id' => $venueWithSportB->id,
        ]);
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

        $nearVenue = $this->createDummyVenue([
            'latitude' => -12.9720,
            'longitude' => -38.5020,
        ]);

        $farVenue = $this->createDummyVenue([
            'latitude' => -10.0000,
            'longitude' => -30.0000,
        ]);

        $response = $this->getJson(route($this->getRouteName(), [
            'per_page' => 10,
            'latitude' => $userLat,
            'longitude' => $userLng,
            'radius_km' => 10,
        ]))->assertOk()->assertJsonFragment([
            'id' => $nearVenue->id,
        ])->assertJsonMissing([
            'id' => $farVenue->id,
        ]);

        $this->assertNotNull($response->json('data.0.distance_in_km'));
    }

    /**
     * Test correctly counts ongoing and upcoming games excluding finished or cancelled status.
     *
     * @return void
     */
    public function test_if_correctly_calculates_ongoing_and_upcoming_game_sessions_count(): void
    {
        $venue = $this->createDummyVenue();
        $now = Carbon::now();

        // upcoming
        $this->createDummyGameSession([
            'venue_id' => $venue->id,
            'start_time' => $now->copy()->addHour(),
            'end_time' => $now->copy()->addHours(2),
            'game_session_status_id' => GameSessionStatusEnum::OPEN->value ?? 1,
        ]);

        // ongoing
        $this->createDummyGameSession([
            'venue_id' => $venue->id,
            'start_time' => $now->copy()->subMinutes(30),
            'end_time' => $now->copy()->addMinutes(30),
            'game_session_status_id' => GameSessionStatusEnum::OPEN->value ?? 1,
        ]);

        // cancelled (ignored)
        $this->createDummyGameSession([
            'venue_id' => $venue->id,
            'start_time' => $now->copy()->addHour(),
            'end_time' => $now->copy()->addHours(2),
            'game_session_status_id' => GameSessionStatusEnum::CANCELLED->value,
        ]);

        // finished (ignored)
        $this->createDummyGameSession([
            'venue_id' => $venue->id,
            'start_time' => $now->copy()->subHours(3),
            'end_time' => $now->copy()->subHours(1),
            'game_session_status_id' => GameSessionStatusEnum::FINISHED->value,
        ]);

        $this->getJson(route($this->getRouteName(), [
            'per_page' => 10,
        ]))->assertOk()
            ->assertJsonPath('data.0.id', $venue->id)
            ->assertJsonPath('data.0.game_sessions_count', 4)
            ->assertJsonPath('data.0.upcoming_games_count', 1)
            ->assertJsonPath('data.0.ongoing_games_count', 1);
    }

    /**
     * Data provider with generators for invalid venue filter payloads.
     *
     * @return Generator<string, array{payload: array<string, mixed>, expectedErrorMessages: array<string, string>}>
     */
    public static function invalidVenueQueryPayloadsProvider(): Generator
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
                'radius_km' => 'abc',
                'latitude' => 'invalid-lat',
                'longitude' => 'invalid-lng',
            ],
            'expectedErrorMessages' => [
                'per_page' => 'O campo por p\u00e1gina deve ser um n\u00famero.',
                'sport_id' => 'O campo esporte deve ser um n\u00famero.',
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
                'radius_km' => 31,
            ],
            'expectedErrorMessages' => [
                'radius_km' => 'O campo raio em km deve ser entre 1 e 30.',
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
