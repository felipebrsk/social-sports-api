<?php

namespace App\Contracts\Services\Authentication;

use App\DTOs\Authentication\EmailVerify;
use App\Exceptions\Authentication\{
    InvalidEmailVerifyDataException,
    UserEmailAlreadyVerifiedException,
};

interface EmailVerifyServiceInterface
{
    /**
     * Send or resend an email verification for the user.
     *
     * @return void
     * @throws UserEmailAlreadyVerifiedException
     */
    public function resend(): void;

    /**
     * Mark the authenticated user's email address as verified.
     *
     * @param EmailVerify $data
     * @return void
     * @throws InvalidEmailVerifyDataException
     */
    public function verify(EmailVerify $data): void;
}
