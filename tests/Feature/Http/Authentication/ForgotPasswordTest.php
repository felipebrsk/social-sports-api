<?php

namespace Tests\Feature\Http\Authentication;

use Generator;
use Mockery;
use App\Models\User;
use Mockery\MockInterface;
use Tests\Feature\BaseIntegrationTesting;
use PHPUnit\Framework\Attributes\DataProvider;
use Illuminate\Support\Facades\Notification;
use App\Notifications\Authentication\QueuedResetPassword;
use App\Exceptions\Authentication\UserRecentlyCreatedTokenException;
use App\Contracts\Repositories\Authentication\ResetPasswordRepositoryInterface;

class ForgotPasswordTest extends BaseIntegrationTesting
{
    /**
     * Get the route name.
     *
     * @return string
     */
    private function getRouteName(): string
    {
        return 'authentication.forgot-password';
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
     * Test validation rules for invalid forgot password payloads using DataProvider.
     *
     * @param array<string, mixed> $payload
     * @param array<string, string> $expectedErrorMessages
     * @return void
     */
    #[DataProvider('invalidForgotPasswordPayloadsProvider')]
    public function test_if_validation_fails_with_invalid_data(
        array $payload,
        array $expectedErrorMessages
    ): void {
        $response = $this->postJson(route($this->getRouteName()), $payload)
            ->assertUnprocessable()
            ->assertJsonValidationErrors(array_keys($expectedErrorMessages));

        foreach ($expectedErrorMessages as $message) {
            $response->assertSee($message);
        }
    }

    /**
     * Test if it returns OK and prevents email enumeration if user does not exist.
     *
     * @return void
     */
    public function test_if_returns_ok_even_if_user_not_found(): void
    {
        $this->postJson(route($this->getRouteName()), [
            'email' => 'nonexistent@gmail.com',
        ])->assertOk()->assertJsonPath('message', 'Um link para a redefinição de senha será enviado para o seu e-mail!');

        Notification::assertNothingSent();
    }

    /**
     * Test if throws exception if user requested a token recently.
     *
     * @return void
     */
    public function test_if_fails_if_recently_created_token(): void
    {
        $this->mock(ResetPasswordRepositoryInterface::class, function (MockInterface $mock) {
            $mock
                ->shouldReceive('recentlyCreatedToken')
                ->with(Mockery::on(fn(User $arg) => $arg->id === $this->user->id))
                ->once()
                ->andReturnTrue();
        });

        $this->withoutExceptionHandling();
        $this->expectException(UserRecentlyCreatedTokenException::class);

        $this->postJson(route($this->getRouteName()), [
            'email' => $this->user->email,
        ]);
    }

    /**
     * Test if can request a password reset link successfully.
     *
     * @return void
     */
    public function test_if_can_request_password_reset_link(): void
    {
        $this->postJson(route($this->getRouteName()), [
            'email' => $this->user->email,
        ])->assertOk()->assertJsonPath('message', 'Um link para a redefinição de senha será enviado para o seu e-mail!');

        Notification::assertSentTo(
            [$this->user],
            QueuedResetPassword::class,
            function (QueuedResetPassword $notification) {
                return ! empty($notification->token);
            }
        );
    }

    /**
     * Data provider with generators for invalid forgot password validation scenarios.
     *
     * @return Generator<string, array{payload: array<string, mixed>, expectedErrorMessages: array<string, string>}>
     */
    public static function invalidForgotPasswordPayloadsProvider(): Generator
    {
        yield 'missing email' => [
            'payload' => [],
            'expectedErrorMessages' => [
                'email' => 'O campo email \u00e9 obrigat\u00f3rio.',
            ],
        ];

        yield 'invalid email format' => [
            'payload' => [
                'email' => 'invalid-email-format',
            ],
            'expectedErrorMessages' => [
                'email' => 'O campo email deve ser um endere\u00e7o de e-mail v\u00e1lido.',
            ],
        ];

        yield 'invalid email' => [
            'payload' => [
                'email' => 'invalid@co.i',
            ],
            'expectedErrorMessages' => [
                'email' => 'O campo email deve ser um endere\u00e7o de e-mail v\u00e1lido.',
            ],
        ];
    }
}
