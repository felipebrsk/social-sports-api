<?php

namespace Tests\Unit\Requests\Authentication;

use Generator;
use Tests\Contracts\Requests\BaseRequestTesting;
use App\Http\Requests\Authentication\LoginRequest;
use PHPUnit\Framework\Attributes\{
    CoversClass,
    DataProvider,
};

/**
 * Class LoginRequestTest
 *
 * @package Tests\Unit\Requests\Enrollment
 */
#[CoversClass(LoginRequest::class)]
class LoginRequestTest extends BaseRequestTesting
{
    /**
     * {@inheritDoc}
     */
    public function request(): string
    {
        return LoginRequest::class;
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
                'email' => 'valid@gmail.com',
                'password' => 'pass12345678',
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
                'email' => 'O campo email é obrigatório.',
                'password' => 'O campo senha é obrigatório.',
            ],
        ];

        yield 'invalid data types' => [
            'data' => [
                'email' => 123,
                'password' => 123,
            ],
            'expectedErrors' => [
                'email' => 'O campo email deve ser uma string.',
                'password' => 'O campo senha deve ser uma string.',
            ],
        ];

        yield 'email should be a valid email' => [
            'data' => [
                'email' => 'invalid@co',
            ],
            'expectedErrors' => [
                'email' => 'O campo email deve ser um endereço de e-mail válido.',
            ],
        ];

        yield 'email must have 255 chars at maximum' => [
            'data' => [
                'email' => str_repeat('A', 250) . '@gmail.com',
            ],
            'expectedErrors' => [
                'email' => 'O campo email não pode ser superior a 255 caracteres.',
            ],
        ];
    }
}
