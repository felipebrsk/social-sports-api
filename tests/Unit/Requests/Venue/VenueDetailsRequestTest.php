<?php

namespace Tests\Unit\Requests\Venue;

use Generator;
use App\Http\Requests\Venue\VenueDetailsRequest;
use Tests\Contracts\Requests\BaseRequestTesting;
use PHPUnit\Framework\Attributes\{
    CoversClass,
    DataProvider,
};

/**
 * Class VenueDetailsRequestTest
 *
 * @package Tests\Unit\Requests\Venue
 */
#[CoversClass(VenueDetailsRequest::class)]
class VenueDetailsRequestTest extends BaseRequestTesting
{
    /**
     * {@inheritDoc}
     */
    public function request(): string
    {
        return VenueDetailsRequest::class;
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
        yield 'empty payload is valid' => [
            'data' => [],
        ];

        yield 'all fields with valid values' => [
            'data' => [
                'latitude' => fake()->latitude(),
                'longitude' => fake()->longitude(),
            ],
        ];

        yield 'numeric as string' => [
            'data' => [
                'latitude' => '-10.938241',
                'longitude' => '-34.2931823',
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
        yield 'invalid data types' => [
            'data' => [
                'latitude' => 'abc',
                'longitude' => 'abc',
            ],
            'expectedErrors' => [
                'latitude' => 'O campo latitude deve ser um número.',
                'longitude' => 'O campo longitude deve ser um número.',
            ],
        ];

        yield 'latitude exceeds negative limit' => [
            'data' => [
                'latitude' => -91.293123,
            ],
            'expectedErrors' => [
                'latitude' => 'O campo latitude deve ser entre -90 e 90.',
            ],
        ];

        yield 'latitude exceeds positive limit' => [
            'data' => [
                'latitude' => 91.293123,
            ],
            'expectedErrors' => [
                'latitude' => 'O campo latitude deve ser entre -90 e 90.',
            ],
        ];

        yield 'longitude exceeds negative limit' => [
            'data' => [
                'longitude' => -181.293123,
            ],
            'expectedErrors' => [
                'longitude' => 'O campo longitude deve ser entre -180 e 180.',
            ],
        ];

        yield 'longitude exceeds positive limit' => [
            'data' => [
                'longitude' => 181.293123,
            ],
            'expectedErrors' => [
                'longitude' => 'O campo longitude deve ser entre -180 e 180.',
            ],
        ];
    }
}
