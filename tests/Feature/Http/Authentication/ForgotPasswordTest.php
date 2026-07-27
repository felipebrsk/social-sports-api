<?php

namespace Tests\Feature\Http\Authentication;

use Mockery;
use App\Models\User;
use Mockery\MockInterface;
use Tests\Feature\BaseIntegrationTesting;
use Illuminate\Support\Facades\Notification;
use Illuminate\Auth\Notifications\ResetPassword;
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
     * Test if validation fails with an invalid email.
     *
     * @return void
     */
    public function test_if_validation_fails_with_invalid_data(): void
    {
        $this->postJson(route($this->getRouteName()), [
            'email' => 'invalid-email-format',
        ])->assertUnprocessable()->assertJsonValidationErrors(['email']);
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
                ->with(Mockery::on(fn (User $arg) => $arg->id === $this->user->id))
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

        Notification::assertSentTo($this->user, ResetPassword::class);
    }
}
