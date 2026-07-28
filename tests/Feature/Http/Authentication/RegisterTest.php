<?php

namespace Tests\Feature\Http\Authentication;

use Generator;
use App\Models\User;
use Tests\Feature\BaseIntegrationTesting;
use PHPUnit\Framework\Attributes\DataProvider;
use App\Notifications\Authentication\QueuedVerifyEmail;
use Illuminate\Support\Facades\{
    Hash,
    Notification,
};

class RegisterTest extends BaseIntegrationTesting
{
    /**
     * Get the route name.
     *
     * @return string
     */
    private function getRouteName(): string
    {
        return 'authentication.register';
    }

    /**
     * Setup new test environments.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        Notification::fake();
    }

    /**
     * Test validation rules for invalid register payloads using DataProvider.
     *
     * @param array<string, mixed> $payload
     * @param array<string, string> $expectedErrorMessages
     * @return void
     */
    #[DataProvider('invalidRegisterPayloadsProvider')]
    public function test_if_fails_with_invalid_register_payloads(
        array $payload,
        array $expectedErrorMessages,
    ): void {
        $response = $this->postJson(route($this->getRouteName()), $payload)
            ->assertUnprocessable()
            ->assertJsonValidationErrors(array_keys($expectedErrorMessages));

        foreach ($expectedErrorMessages as $field => $message) {
            $response->assertSee($message);
        }
    }

    /**
     * Test if fails when registering with an email that is already taken.
     *
     * @return void
     */
    public function test_if_fails_when_email_is_already_taken(): void
    {
        $existingUser = $this->createDummyUser([
            'email' => 'registered@example.com',
        ]);

        $payload = [
            'name' => 'John Doe',
            'email' => $existingUser->email,
            'password' => 'StrongP@ssw0rd2',
            'password_confirmation' => 'StrongP@ssw0rd2',
        ];

        $this->postJson(route($this->getRouteName()), $payload)
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['email']);
    }

    /**
     * Test if can register user successfully and generate token.
     *
     * @return void
     */
    public function test_if_can_register_user_successfully(): void
    {
        $payload = [
            'name' => 'John Doe',
            'email' => 'john.doe@gmail.com',
            'password' => 'StrongP@ssw0rd2',
            'password_confirmation' => 'StrongP@ssw0rd2',
        ];

        $this->postJson(route($this->getRouteName()), $payload)
            ->assertCreated()
            ->assertJsonPath('data.message', 'Usuário cadastrado com sucesso!')
            ->assertJsonStructure([
                'data' => [
                    'token',
                    'message',
                ],
            ]);

        $this->assertDatabaseHas('users', [
            'name' => $payload['name'],
            'email' => $payload['email'],
        ]);

        $user = User::where('email', $payload['email'])->firstOrFail();

        $this->assertTrue(Hash::check($payload['password'], $user->password));

        $this->assertDatabaseHas('profiles', [
            'user_id' => $user->id,
        ]);

        Notification::assertSentTo($user, QueuedVerifyEmail::class);
    }

    /**
     * Data provider with generators for invalid register validation scenarios.
     *
     * @return Generator<string, array{payload: array<string, mixed>, expectedErrorMessages: array<string, string>}>
     */
    public static function invalidRegisterPayloadsProvider(): Generator
    {
        yield 'empty payload' => [
            'payload' => [],
            'expectedErrorMessages' => [
                'name' => 'O campo nome \u00e9 obrigat\u00f3rio.',
                'email' => 'O campo email \u00e9 obrigat\u00f3rio.',
                'password' => 'O campo senha \u00e9 obrigat\u00f3rio.',
            ],
        ];

        yield 'invalid data types' => [
            'payload' => [
                'name' => 123,
                'email' => 123,
                'password' => 123,
            ],
            'expectedErrorMessages' => [
                'name' => 'O campo nome deve ser uma string.',
                'email' => 'O campo email deve ser uma string.',
                'password' => 'O campo senha deve ser uma string.',
            ],
        ];

        yield 'name must have 255 chars at maximum' => [
            'payload' => [
                'name' => str_repeat('A', 250) . '@gmail.com',
            ],
            'expectedErrorMessages' => [
                'name' => 'O campo nome n\u00e3o pode ser superior a 255 caracteres.',
            ],
        ];

        yield 'invalid email format' => [
            'payload' => [
                'name' => 'John Doe',
                'email' => 'invalid-email-format',
                'password' => 'StrongP@ssw0rd2',
                'password_confirmation' => 'StrongP@ssw0rd2',
            ],
            'expectedErrorMessages' => [
                'email' => 'O campo email deve ser um endere\u00e7o de e-mail v\u00e1lido.',
            ],
        ];

        yield 'invalid email' => [
            'payload' => [
                'name' => 'John Doe',
                'email' => 'invalid@co.i',
                'password' => 'StrongP@ssw0rd2',
                'password_confirmation' => 'StrongP@ssw0rd2',
            ],
            'expectedErrorMessages' => [
                'email' => 'O campo email deve ser um endere\u00e7o de e-mail v\u00e1lido.',
            ],
        ];

        yield 'email must have 255 chars at maximum' => [
            'payload' => [
                'email' => str_repeat('A', 250) . '@gmail.com',
            ],
            'expectedErrorMessages' => [
                'email' => 'O campo email n\u00e3o pode ser superior a 255 caracteres.',
            ],
        ];

        yield 'password should not have more than 16 chars' => [
            'payload' => [
                'password' => str_repeat('A', 17),
            ],
            'expectedErrorMessages' => [
                'password' => 'O campo senha n\u00e3o pode ser superior a 16 caracteres.',
            ],
        ];

        yield 'password must contains at least one letter' => [
            'payload' => [
                'password' => '12345678',
                'password_confirmation' => '12345678',
            ],
            'expectedErrorMessages' => [
                'password' => 'O campo senha deve conter ao menos um caracter mai\u00fasculo e min\u00fasculo.',
            ],
        ];

        yield 'password must contains at least one number' => [
            'payload' => [
                'password' => 'AbCDEFGH!',
                'password_confirmation' => 'AbCDEFGH!',
            ],
            'expectedErrorMessages' => [
                'password' => 'O campo senha deve conter ao menos um n\u00famero.',
            ],
        ];

        yield 'password must contains at least one mixed case' => [
            'payload' => [
                'password' => 'ABCDEFGH!1',
                'password_confirmation' => 'ABCDEFGH!1',
            ],
            'expectedErrorMessages' => [
                'password' => 'O campo senha deve conter ao menos um caracter mai\u00fasculo e min\u00fasculo.',
            ],
        ];

        yield 'password must contains at least one symbol' => [
            'payload' => [
                'password' => 'ABCDEFGH1a',
                'password_confirmation' => 'ABCDEFGH1a',
            ],
            'expectedErrorMessages' => [
                'password' => 'O campo senha deve conter ao menos um s\u00edmbolo.',
            ],
        ];

        yield 'password must not be compromised' => [
            'payload' => [
                'password' => 'Admin1234!',
                'password_confirmation' => 'Admin1234!',
            ],
            'expectedErrorMessages' => [
                'password' => 'A senha informada apareceu em um vazamento de dados. Por favor, escolha uma senha diferente.',
            ],
        ];

        yield 'password must be confirmed' => [
            'payload' => [
                'password' => 'Strong@Pass1!3',
            ],
            'expectedErrorMessages' => [
                'password' => 'O campo confirma\u00e7\u00e3o de senha n\u00e3o confere.',
            ],
        ];

        yield 'password confirmation does not match' => [
            'payload' => [
                'password' => 'Strong@Pass1!3',
                'password_confirmation' => 'Strong@Pass1!34',
            ],
            'expectedErrorMessages' => [
                'password' => 'O campo confirma\u00e7\u00e3o de senha n\u00e3o confere.',
            ],
        ];
    }
}
