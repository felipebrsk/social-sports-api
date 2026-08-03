<?php

namespace Tests\Unit\Requests\Authentication;

use Generator;
use Tests\Contracts\Requests\BaseRequestTesting;
use App\Http\Requests\Authentication\GoogleRequest;
use PHPUnit\Framework\Attributes\{
    CoversClass,
    DataProvider,
};

/**
 * Class GoogleRequestTest
 *
 * @package Tests\Unit\Requests\Enrollment
 */
#[CoversClass(GoogleRequest::class)]
class GoogleRequestTest extends BaseRequestTesting
{
    /**
     * {@inheritDoc}
     */
    public function request(): string
    {
        return GoogleRequest::class;
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
        yield 'all valid payload' => [
            'data' => [
                'id_token' => fake()->word(),
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
        yield 'missing all required fields' => [
            'data' => [],
            'expectedErrors' => [
                'id_token' => 'O campo token é obrigatório.',
            ],
        ];

        yield 'invalid data types' => [
            'data' => [
                'id_token' => 123,
            ],
            'expectedErrors' => [
                'id_token' => 'O campo token deve ser uma string.',
            ],
        ];
    }
}
