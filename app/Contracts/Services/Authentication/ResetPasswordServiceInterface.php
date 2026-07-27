<?php

namespace App\Contracts\Services\Authentication;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\DTOs\Authentication\{
    ResetPassword,
    ForgotPassword,
};
use App\Exceptions\Authentication\{
    InvalidTokenException,
    UserIsBlockedException,
    UserRecentlyCreatedTokenException,
};

interface ResetPasswordServiceInterface
{
    /**
     * Send reset notification.
     *
     * @param ForgotPassword $data
     * @return void
     * @throws UserRecentlyCreatedTokenException
     */
    public function sendResetNotification(ForgotPassword $data): void;

    /**
     * Reset suer password.
     *
     * @param ResetPassword $data
     * @return void
     * @throws InvalidTokenException
     * @throws ModelNotFoundException
     * @throws UserIsBlockedException
     */
    public function resetPassword(ResetPassword $data): void;
}
