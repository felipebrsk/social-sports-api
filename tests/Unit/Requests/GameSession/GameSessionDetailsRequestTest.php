<?php

namespace Tests\Unit\Requests\GameSession;

use Generator;
use Tests\Contracts\Requests\BaseRequestTesting;
use App\Http\Requests\GameSession\GameSessionDetailsRequest;
use PHPUnit\Framework\Attributes\{
    CoversClass,
    DataProvider,
};

#[CoversClass(GameSessionDetailsRequest::class)]
class GameSessionDetailsRequestTest extends BaseRequestTesting
{
    /**
     * {@inheritDoc}
     */
    public function request(): string
    {
        return GameSessionDetailsRequest::class;
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
            'data' => [],
        ];

        yield 'all valid fields combined can proceed' => [
            'data' => [
                'latitude' => fake()->latitude(),
                'longitude' => fake()->longitude(),
            ],
        ];

        yield 'numeric values passed as valid strings' => [
            'data' => [
                'latitude' => '-12.9714',
                'longitude' => '-38.5014',
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
        yield 'invalid numeric fields' => [
            'data' => [
                'latitude' => 'abc',
                'longitude' => 'abc',
            ],
            'expectedErrors' => [
                'latitude' => 'O campo latitude deve ser um número.',
                'longitude' => 'O campo longitude deve ser um número.',
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
    }
}
