<?php

namespace Tests\Unit\Services\Authentication;

use Mockery;
use Tests\TestCase;
use App\Models\User;
use Mockery\MockInterface;
use App\Contracts\Services\UserServiceInterface;
use App\Services\Authentication\ResetPasswordService;
use App\Notifications\Authentication\PasswordReseted;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Contracts\Repositories\Authentication\ResetPasswordRepositoryInterface;
use App\DTOs\Authentication\{
    ResetPassword,
    ForgotPassword,
};
use App\Exceptions\Authentication\{
    InvalidTokenException,
    UserIsBlockedException,
    UserRecentlyCreatedTokenException,
};

class ResetPasswordServiceTest extends TestCase
{
    /**
     * The user service mock.
     */
    private UserServiceInterface&MockInterface $userServiceMock;

    /**
     * The reset password repository mock.
     */
    private ResetPasswordRepositoryInterface&MockInterface $resetPasswordRepositoryMock;

    /**
     * The service instance under test.
     */
    private ResetPasswordService $resetPasswordService;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->userServiceMock = Mockery::mock(UserServiceInterface::class);
        $this->resetPasswordRepositoryMock = Mockery::mock(ResetPasswordRepositoryInterface::class);

        $this->resetPasswordService = new ResetPasswordService(
            $this->userServiceMock,
            $this->resetPasswordRepositoryMock,
        );
    }

    /**
     * Test if sendResetNotification does nothing when user email is not found.
     *
     * @return void
     */
    public function test_send_reset_notification_does_nothing_if_user_not_found(): void
    {
        $this->userServiceMock
            ->shouldReceive('findBy')
            ->once()
            ->with('email', 'notfound@example.com')
            ->andReturnNull();

        $this->resetPasswordRepositoryMock->shouldNotReceive('recentlyCreatedToken');

        $dto = new ForgotPassword('notfound@example.com');

        $this->resetPasswordService->sendResetNotification($dto);
    }

    /**
     * Test if sendResetNotification throws UserRecentlyCreatedTokenException when a token exists.
     *
     * @return void
     */
    public function test_send_reset_notification_throws_exception_if_token_recently_created(): void
    {
        $user = Mockery::mock(User::class);

        $this->userServiceMock
            ->shouldReceive('findBy')
            ->once()
            ->with('email', 'user@example.com')
            ->andReturn($user);

        $this->resetPasswordRepositoryMock
            ->shouldReceive('recentlyCreatedToken')
            ->once()
            ->with($user)
            ->andReturnTrue();

        $this->expectException(UserRecentlyCreatedTokenException::class);

        $dto = new ForgotPassword('user@example.com');

        $this->resetPasswordService->sendResetNotification($dto);
    }

    /**
     * Test if sendResetNotification generates a token and fires the notification on success.
     *
     * @return void
     */
    public function test_send_reset_notification_creates_token_and_sends_notification(): void
    {
        $user = Mockery::mock(User::class);

        $this->userServiceMock
            ->shouldReceive('findBy')
            ->once()
            ->with('email', 'user@example.com')
            ->andReturn($user);

        $this->resetPasswordRepositoryMock
            ->shouldReceive('recentlyCreatedToken')
            ->once()
            ->with($user)
            ->andReturnFalse();

        $this->resetPasswordRepositoryMock
            ->shouldReceive('createResetToken')
            ->once()
            ->with($user)
            ->andReturn('generated-reset-token-123');

        $user
            ->shouldReceive('sendPasswordResetNotification')
            ->once()
            ->with('generated-reset-token-123');

        $dto = new ForgotPassword('user@example.com');

        $this->resetPasswordService->sendResetNotification($dto);
    }

    /**
     * Test if resetPassword throws a ModelNotFoundException when input email doesn't exist.
     *
     * @return void
     */
    public function test_reset_password_throws_model_not_found_exception_if_user_absent(): void
    {
        $this->userServiceMock
            ->shouldReceive('findBy')
            ->once()
            ->with('email', 'missing@example.com')
            ->andReturnNull();

        $this->expectException(ModelNotFoundException::class);
        $this->expectExceptionMessage('Usuário não encontrado.');

        $dto = new ResetPassword(
            'missing@example.com',
            'some-token',
            'new-password',
        );

        $this->resetPasswordService->resetPassword($dto);
    }

    /**
     * Test if resetPassword updates the user password, flushes the tokens, and dispatches confirmation.
     *
     * @return void
     */
    public function test_reset_password_updates_password_deletes_token_and_notifies_user(): void
    {
        $user = Mockery::mock(User::class);
        $user
            ->shouldReceive('getAttribute')
            ->with('blocked')
            ->andReturnFalse();

        $dto = new ResetPassword(
            'user@example.com',
            'valid-token',
            'secret123',
        );

        $this->userServiceMock
            ->shouldReceive('findBy')
            ->once()
            ->with('email', $dto->email)
            ->andReturn($user);

        $this->resetPasswordRepositoryMock
            ->shouldReceive('exists')
            ->once()
            ->with($user, $dto->token)
            ->andReturnTrue();

        $user
            ->shouldReceive('update')
            ->once()
            ->with(['password' => 'secret123'])
            ->andReturnTrue();

        $this->resetPasswordRepositoryMock
            ->shouldReceive('delete')
            ->once()
            ->with($user);

        $user
            ->shouldReceive('notify')
            ->once()
            ->with(Mockery::type(PasswordReseted::class));

        $this->resetPasswordService->resetPassword($dto);
    }

    /**
     * Test if canResetPassword throws InvalidTokenException when the token does not match records.
     *
     * @return void
     */
    public function test_can_reset_password_throws_invalid_token_exception_if_token_not_exists(): void
    {
        $user = Mockery::mock(User::class);

        $this->resetPasswordRepositoryMock
            ->shouldReceive('exists')
            ->once()
            ->with($user, 'invalid-token')
            ->andReturnFalse();

        $this->expectException(InvalidTokenException::class);

        $this->resetPasswordService->canResetPassword($user, 'invalid-token');
    }

    /**
     * Test if canResetPassword throws UserIsBlockedException when user password field is empty.
     *
     * @return void
     */
    public function test_can_reset_password_throws_user_access_blocked_exception_if_user_is_blocked(): void
    {
        $user = Mockery::mock(User::class);
        $user
            ->shouldReceive('getAttribute')
            ->with('blocked')
            ->andReturnTrue();

        $this->resetPasswordRepositoryMock
            ->shouldReceive('exists')
            ->once()
            ->with($user, 'valid-token')
            ->andReturnTrue();

        $this->expectException(UserIsBlockedException::class);

        $this->resetPasswordService->canResetPassword($user, 'valid-token');
    }
}
