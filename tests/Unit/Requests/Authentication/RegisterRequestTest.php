<?php

namespace Tests\Unit\Requests\Authentication;

use Generator;
use Tests\Contracts\Requests\BaseRequestTesting;
use App\Http\Requests\Authentication\RegisterRequest;
use PHPUnit\Framework\Attributes\{
    CoversClass,
    DataProvider,
};

/**
 * Class RegisterRequestTest
 *
 * @package Tests\Unit\Requests\Enrollment
 */
#[CoversClass(RegisterRequest::class)]
class RegisterRequestTest extends BaseRequestTesting
{
    /**
     * {@inheritDoc}
     */
    public function request(): string
    {
        return RegisterRequest::class;
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
        $this->mockPresenceVerifier(0);

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
        $this->mockPresenceVerifier(1);

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
                'name' => 'John Doe',
                'email' => 'valid@gmail.com',
                'password' => 'Strong@Pass1!3',
                'password_confirmation' => 'Strong@Pass1!3',
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
                'name' => 'O campo nome é obrigatório',
                'email' => 'O campo email é obrigatório.',
                'password' => 'O campo senha é obrigatório.',
            ],
        ];

        yield 'invalid data types' => [
            'data' => [
                'name' => 123,
                'email' => 123,
                'password' => 123,
            ],
            'expectedErrors' => [
                'name' => 'O campo nome deve ser uma string',
                'email' => 'O campo email deve ser uma string.',
                'password' => 'O campo senha deve ser uma string.',
            ],
        ];

        yield 'name must have 255 chars at maximum' => [
            'data' => [
                'name' => str_repeat('A', 250) . '@gmail.com',
            ],
            'expectedErrors' => [
                'name' => 'O campo nome não pode ser superior a 255 caracteres.',
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

        yield 'email is already taken' => [
            'data' => [
                'email' => 'valid@gmail.com',
            ],
            'expectedErrors' => [
                'email' => 'O campo email já está sendo utilizado.',
            ],
        ];

        yield 'password should not have more than 16 chars' => [
            'data' => [
                'password' => str_repeat('A', 17),
            ],
            'expectedErrors' => [
                'password' => 'O campo senha não pode ser superior a 16 caracteres.',
            ],
        ];

        yield 'password must contains at least one letter' => [
            'data' => [
                'password' => '12345678',
                'password_confirmation' => '12345678',
            ],
            'expectedErrors' => [
                'password' => 'O campo senha deve conter ao menos um caracter maiúsculo e minúsculo.',
            ],
        ];

        yield 'password must contains at least one number' => [
            'data' => [
                'password' => 'AbCDEFGH!',
                'password_confirmation' => 'AbCDEFGH!',
            ],
            'expectedErrors' => [
                'password' => 'O campo senha deve conter ao menos um número.',
            ],
        ];

        yield 'password must contains at least one mixed case' => [
            'data' => [
                'password' => 'ABCDEFGH!1',
                'password_confirmation' => 'ABCDEFGH!1',
            ],
            'expectedErrors' => [
                'password' => 'O campo senha deve conter ao menos um caracter maiúsculo e minúsculo.',
            ],
        ];

        yield 'password must contains at least one symbol' => [
            'data' => [
                'password' => 'ABCDEFGH1a',
                'password_confirmation' => 'ABCDEFGH1a',
            ],
            'expectedErrors' => [
                'password' => 'O campo senha deve conter ao menos um símbolo.',
            ],
        ];

        yield 'password must not be compromised' => [
            'data' => [
                'password' => 'Admin1234!',
                'password_confirmation' => 'Admin1234!',
            ],
            'expectedErrors' => [
                'password' => 'A senha informada apareceu em um vazamento de dados. Por favor, escolha uma senha diferente.',
            ],
        ];

        yield 'password must be confirmed' => [
            'data' => [
                'password' => 'Strong@Pass1!3',
            ],
            'expectedErrors' => [
                'password' => 'O campo confirmação de senha não confere.',
            ],
        ];

        yield 'password confirmation does not match' => [
            'data' => [
                'password' => 'Strong@Pass1!3',
                'password_confirmation' => 'Strong@Pass1!34',
            ],
            'expectedErrors' => [
                'password' => 'O campo confirmação de senha não confere.',
            ],
        ];
    }
}
