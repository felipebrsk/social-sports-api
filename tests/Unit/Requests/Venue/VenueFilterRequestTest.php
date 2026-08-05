<?php

namespace Tests\Unit\Requests\Venue;

use Generator;
use Mockery\MockInterface;
use App\Http\Requests\Venue\VenueFilterRequest;
use Tests\Contracts\Requests\BaseRequestTesting;
use App\Contracts\Repositories\VenueRepositoryInterface;
use PHPUnit\Framework\Attributes\{
    CoversClass,
    DataProvider,
};

/**
 * Class VenueFilterRequestTest
 *
 * @package Tests\Unit\Requests\Venue
 */
#[CoversClass(VenueFilterRequest::class)]
class VenueFilterRequestTest extends BaseRequestTesting
{
    /**
     * {@inheritDoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->mock(VenueRepositoryInterface::class, function (MockInterface $mock) {
            $mock->shouldReceive('getAllowedSorts')
                ->andReturn(['id', 'name', 'city', 'state', 'created_at', 'updated_at']);
        });
    }

    /**
     * {@inheritDoc}
     */
    public function request(): string
    {
        return VenueFilterRequest::class;
    }

    /**
     * Test that valid data passes validation.
     *
     * @param array<string, mixed> $data
     * @return void
     */
    #[DataProvider('validDataProvider')]
    public function test_valid_data_passes(array $data): void
    {
        $this->reloadRequestRules();

        $this->assertTrue($this->validate($data), 'Validation should pass for the provided valid data.');
    }

    /**
     * Test that invalid data fails validation with the correct error messages.
     *
     * @param array<string, mixed> $data
     * @param array<string, string> $expectedErrors
     * @return void
     */
    #[DataProvider('invalidDataProvider')]
    public function test_invalid_data_fails(array $data, array $expectedErrors): void
    {
        $this->assertFalse($this->validate($data), 'Validation should fail for the provided invalid data.');

        $errors = $this->getValidationErrors($data);

        foreach ($expectedErrors as $field => $expectedMessage) {
            $this->assertArrayHasKey($field, $errors, "Error key for '$field' was not found.");
            $this->assertStringContainsString($expectedMessage, $errors[$field][0], "The error message for '$field' did not match the expected value.");
        }
    }

    /**
     * Data provider for valid validation cases.
     *
     * @return Generator<string, array{data: array<string, mixed>}>
     */
    public static function validDataProvider(): Generator
    {
        yield 'only required fields can proceed' => [
            'data' => [
                'per_page' => fake()->numberBetween(1, 50),
            ],
        ];

        yield 'all valid fields combined can proceed' => [
            'data' => [
                'id' => 1,
                'sport_id' => 101,
                'city' => fake()->city(),
                'state' => 'BA',
                'search' => 'Arena Beach',
                'per_page' => 20,
                'radius_km' => 15,
                'latitude' => fake()->latitude(),
                'longitude' => fake()->longitude(),
                'sort_by' => 'created_at',
                'sort_order' => 'asc',
            ],
        ];

        yield 'numeric values passed as valid strings' => [
            'data' => [
                'id' => '10',
                'sport_id' => '5',
                'per_page' => '50',
                'radius_km' => '30',
                'latitude' => '-12.9714',
                'longitude' => '-38.5014',
            ],
        ];

        yield 'boundary per_page at minimum' => [
            'data' => [
                'per_page' => 1,
            ],
        ];

        yield 'boundary per_page at maximum' => [
            'data' => [
                'per_page' => 50,
            ],
        ];

        yield 'boundary radius_km at minimum' => [
            'data' => [
                'per_page' => 10,
                'radius_km' => 1,
            ],
        ];

        yield 'boundary radius_km at maximum' => [
            'data' => [
                'per_page' => 10,
                'radius_km' => 30,
            ],
        ];

        yield 'sort_order with desc' => [
            'data' => [
                'per_page' => 10,
                'sort_order' => 'desc',
            ],
        ];
    }

    /**
     * Data provider for invalid validation cases.
     *
     * @return Generator<string, array{data: array<string, mixed>, expectedErrors: array<string, string>}>
     */
    public static function invalidDataProvider(): Generator
    {
        yield 'missing required per_page field' => [
            'data' => [],
            'expectedErrors' => [
                'per_page' => 'O campo por página é obrigatório.',
            ],
        ];

        yield 'invalid numeric fields' => [
            'data' => [
                'per_page' => 10,
                'id' => 'not-numeric',
                'sport_id' => 'not-numeric',
                'latitude' => 'abc',
                'longitude' => 'abc',
                'radius_km' => 'abc',
            ],
            'expectedErrors' => [
                'id' => 'O campo id deve ser um número.',
                'sport_id' => 'O campo esporte deve ser um número.',
                'latitude' => 'O campo latitude deve ser um número.',
                'longitude' => 'O campo longitude deve ser um número.',
                'radius_km' => 'O campo raio em km deve ser um número.',
            ],
        ];

        yield 'per_page below minimum' => [
            'data' => [
                'per_page' => 0,
            ],
            'expectedErrors' => [
                'per_page' => 'O campo por página deve ser entre 1 e 50.',
            ],
        ];

        yield 'per_page above maximum' => [
            'data' => [
                'per_page' => 51,
            ],
            'expectedErrors' => [
                'per_page' => 'O campo por página deve ser entre 1 e 50.',
            ],
        ];

        yield 'radius_km below minimum' => [
            'data' => [
                'per_page' => 10,
                'radius_km' => 0,
            ],
            'expectedErrors' => [
                'radius_km' => 'O campo raio em km deve ser entre 1 e 30.',
            ],
        ];

        yield 'radius_km above maximum' => [
            'data' => [
                'per_page' => 10,
                'radius_km' => 31,
            ],
            'expectedErrors' => [
                'radius_km' => 'O campo raio em km deve ser entre 1 e 30.',
            ],
        ];

        yield 'state code invalid size' => [
            'data' => [
                'per_page' => 10,
                'state' => 'BAH',
            ],
            'expectedErrors' => [
                'state' => 'O campo estado deve ser 2 caracteres.',
            ],
        ];

        yield 'string max length exceeded for city and search' => [
            'data' => [
                'per_page' => 10,
                'city' => str_repeat('a', 256),
                'search' => str_repeat('b', 256),
            ],
            'expectedErrors' => [
                'city' => 'O campo cidade não pode ser superior a 255 caracteres.',
                'search' => 'O campo pesquisa não pode ser superior a 255 caracteres.',
            ],
        ];

        yield 'latitude exceeds negative boundary' => [
            'data' => [
                'per_page' => 10,
                'latitude' => -90.0001,
            ],
            'expectedErrors' => [
                'latitude' => 'O campo latitude deve ser entre -90 e 90.',
            ],
        ];

        yield 'latitude exceeds positive boundary' => [
            'data' => [
                'per_page' => 10,
                'latitude' => 90.0001,
            ],
            'expectedErrors' => [
                'latitude' => 'O campo latitude deve ser entre -90 e 90.',
            ],
        ];

        yield 'longitude exceeds negative boundary' => [
            'data' => [
                'per_page' => 10,
                'longitude' => -180.0001,
            ],
            'expectedErrors' => [
                'longitude' => 'O campo longitude deve ser entre -180 e 180.',
            ],
        ];

        yield 'longitude exceeds positive boundary' => [
            'data' => [
                'per_page' => 10,
                'longitude' => 180.0001,
            ],
            'expectedErrors' => [
                'longitude' => 'O campo longitude deve ser entre -180 e 180.',
            ],
        ];

        yield 'invalid sort_by column' => [
            'data' => [
                'per_page' => 10,
                'sort_by' => 'unallowed_column',
            ],
            'expectedErrors' => [
                'sort_by' => 'O campo ordenar por selecionado é inválido.',
            ],
        ];

        yield 'invalid sort_order value' => [
            'data' => [
                'per_page' => 10,
                'sort_order' => 'invalid_order',
            ],
            'expectedErrors' => [
                'sort_order' => 'O campo ordem selecionado é inválido.',
            ],
        ];
    }
}
