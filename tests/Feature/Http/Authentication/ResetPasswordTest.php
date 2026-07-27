<?php

namespace Tests\Feature\Http\Authentication;

use Generator;
use Mockery;
use App\Models\User;
use Mockery\MockInterface;
use Tests\Feature\BaseIntegrationTesting;
use PHPUnit\Framework\Attributes\DataProvider;
use App\Notifications\Authentication\PasswordReseted;
use App\Contracts\Repositories\Authentication\ResetPasswordRepositoryInterface;
use Illuminate\Support\Facades\{
    Hash,
    Notification,
};

class ResetPasswordTest extends BaseIntegrationTesting
{
    /**
     * Get the route name.
     *
     * @return string
     */
    private function getRouteName(): string
    {
        return 'authentication.reset-password';
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
     * Test if throws exception when user is not found by email.
     *
     * @return void
     */
    public function test_if_fails_if_user_not_found(): void
    {
        $this->postJson(route($this->getRouteName()), [
            'email' => 'nonexistent@gmail.com',
            'token' => 'any-token',
            'password' => 'StrongP@ssw0rd2026!#Custom',
            'password_confirmation' => 'StrongP@ssw0rd2026!#Custom',
        ])->assertUnprocessable()->assertSee('O campo email selecionado \u00e9 inv\u00e1lido.');
    }

    /**
     * Test if throws exception when the reset token is invalid.
     *
     * @return void
     */
    public function test_if_fails_with_invalid_token(): void
    {
        $invalidToken = 'invalid-token-string';

        $user = $this->createDummyUser([
            'email' => 'valid_user@gmail.com',
        ]);

        $this->mock(ResetPasswordRepositoryInterface::class, function (MockInterface $mock) use ($user, $invalidToken) {
            $mock
                ->shouldReceive('exists')
                ->with(Mockery::on(fn(User $arg) => $arg->id === $user->id), $invalidToken)
                ->once()
                ->andReturnFalse();
        });

        $this->postJson(route($this->getRouteName()), [
            'email' => $user->email,
            'token' => $invalidToken,
            'password' => 'StrongP@ssw0rd2',
            'password_confirmation' => 'StrongP@ssw0rd2',
        ])->assertBadRequest()->assertSee('N\u00e3o conseguimos validar a sua solicita\u00e7\u00e3o de redefini\u00e7\u00e3o de senha. Por favor, tente novamente.');
    }

    /**
     * Test if throws exception when the user is blocked.
     *
     * @return void
     */
    public function test_if_fails_if_user_access_is_blocked(): void
    {
        $user = $this->createDummyUser([
            'blocked' => true,
            'email' => 'valid_blocked@gmail.com',
        ]);

        $validToken = 'valid-token-string';

        $this->mock(ResetPasswordRepositoryInterface::class, function (MockInterface $mock) use ($user, $validToken) {
            $mock
                ->shouldReceive('exists')
                ->with(Mockery::on(fn(User $arg) => $arg->id === $user->id), $validToken)
                ->once()
                ->andReturnTrue();
        });

        $this->postJson(route($this->getRouteName()), [
            'email' => $user->email,
            'token' => $validToken,
            'password' => 'StrongP@ssw0rd2',
            'password_confirmation' => 'StrongP@ssw0rd2',
        ])->assertForbidden()->assertSee('O acesso do seu usu\u00e1rio foi restrito na plataforma.');
    }

    /**
     * Test validation rules for invalid password payloads using DataProvider.
     *
     * @param array<string, mixed> $payload
     * @param string $expectedErrorField
     * @return void
     */
    #[DataProvider('invalidPasswordPayloadsProvider')]
    public function test_if_fails_with_invalid_password_payloads(array $payload, string $expectedErrorField): void
    {
        $user = $this->createDummyUser([
            'email' => 'valid_user_validation@gmail.com',
        ]);

        $data = [
            'email' => $user->email,
            'token' => 'valid-token-string',
            ...$payload,
        ];

        $this->postJson(route($this->getRouteName()), $data)
            ->assertUnprocessable()
            ->assertJsonValidationErrors([$expectedErrorField]);
    }

    /**
     * Test if the user can successfully reset their password.
     *
     * @return void
     */
    public function test_if_can_reset_password_successfully(): void
    {
        $user = $this->createDummyUser([
            'blocked' => false,
            'password' => 'OldPassword123!',
            'email' => 'valid_success@gmail.com',
        ]);

        $validToken = 'valid-token-string';
        $newPassword = 'StrongP@ssw0rd2';

        $this->mock(ResetPasswordRepositoryInterface::class, function (MockInterface $mock) use ($user, $validToken) {
            $mock
                ->shouldReceive('exists')
                ->with(Mockery::on(fn(User $arg) => $arg->id === $user->id), $validToken)
                ->once()
                ->andReturnTrue();

            $mock
                ->shouldReceive('delete')
                ->with(Mockery::on(fn(User $arg) => $arg->id === $user->id))
                ->once();
        });

        $this->postJson(route($this->getRouteName()), [
            'email' => $user->email,
            'token' => $validToken,
            'password' => $newPassword,
            'password_confirmation' => $newPassword,
        ])->assertOk()->assertJsonPath('message', 'A sua senha foi redefinida com sucesso!');

        $user->refresh();

        $this->assertTrue(Hash::check($newPassword, $user->getAuthPassword()));

        Notification::assertSentTo($user, PasswordReseted::class);
    }

    /**
     * Data provider with generators for invalid password validation scenarios.
     *
     * @return Generator<string, array{payload: array<string, mixed>, expectedErrorField: string}>
     */
    public static function invalidPasswordPayloadsProvider(): Generator
    {
        yield 'missing password confirmation' => [
            'payload' => [
                'password' => 'StrongP@ssw0rd2026!#Custom',
            ],
            'expectedErrorField' => 'password',
        ];

        yield 'password confirmation does not match' => [
            'payload' => [
                'password' => 'StrongP@ssw0rd2026!#Custom',
                'password_confirmation' => 'DifferentP@ssw0rd2026!',
            ],
            'expectedErrorField' => 'password',
        ];

        yield 'password too short' => [
            'payload' => [
                'password' => 'Short1!',
                'password_confirmation' => 'Short1!',
            ],
            'expectedErrorField' => 'password',
        ];

        yield 'password missing special character/numbers or leaked' => [
            'payload' => [
                'password' => '12345678',
                'password_confirmation' => '12345678',
            ],
            'expectedErrorField' => 'password',
        ];
    }
}
