<?php

namespace App\Services\Authentication;

use App\Models\User;
use App\Contracts\Services\UserServiceInterface;
use App\Notifications\Authentication\PasswordReseted;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Contracts\Services\Authentication\ResetPasswordServiceInterface;
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

class ResetPasswordService implements ResetPasswordServiceInterface
{
    /**
     * Create a new class instance.
     *
     * @param UserServiceInterface $userService
     * @param ResetPasswordRepositoryInterface $resetPasswordRepository
     * @return void
     */
    public function __construct(
        private readonly UserServiceInterface $userService,
        private readonly ResetPasswordRepositoryInterface $resetPasswordRepository,
    ) {
        //
    }

    /**
     * {@inheritDoc}
     */
    public function sendResetNotification(ForgotPassword $data): void
    {
        $user = $this->userService->findBy('email', $data->email);

        if ($user) {
            if ($this->resetPasswordRepository->recentlyCreatedToken($user)) {
                throw new UserRecentlyCreatedTokenException();
            }

            $token = $this->resetPasswordRepository->createResetToken($user);

            $user->sendPasswordResetNotification($token);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function resetPassword(ResetPassword $data): void
    {
        $user = $this->userService->findBy('email', $data->email);

        if (! $user) {
            throw new ModelNotFoundException('Usuário não encontrado.');
        }

        $this->canResetPassword($user, $data->token);

        $user->update([
            'password' => $data->password,
        ]);

        $this->resetPasswordRepository->delete($user);

        $user->notify(new PasswordReseted());
    }

    /**
     * {@inheritDoc}
     */
    public function canResetPassword(User $user, string $token): void
    {
        if (! $this->resetPasswordRepository->exists($user, $token)) {
            throw new InvalidTokenException();
        }
        if ($user->blocked) {
            throw new UserIsBlockedException();
        }
    }
}
