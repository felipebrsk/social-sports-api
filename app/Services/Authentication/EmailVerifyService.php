<?php

namespace App\Services\Authentication;

use Illuminate\Auth\Events\Verified;
use App\DTOs\Authentication\EmailVerify;
use App\Contracts\Services\UserServiceInterface;
use App\Exceptions\Authentication\{
    InvalidEmailVerifyDataException,
    UserEmailAlreadyVerifiedException,
};
use App\Contracts\Services\Authentication\{
    EmailVerifyServiceInterface,
    AuthContextServiceInterface,
};

class EmailVerifyService implements EmailVerifyServiceInterface
{
    /**
     * Create a new service instance.
     *
     * @param UserServiceInterface $userService
     * @param AuthContextServiceInterface $authContextService
     * @return void
     */
    public function __construct(
        private readonly UserServiceInterface $userService,
        private readonly AuthContextServiceInterface $authContextService,
    ) {
        //
    }

    /**
     * {@inheritDoc}
     */
    public function resend(): void
    {
        $user = $this->authContextService->user();

        if ($user->hasVerifiedEmail()) {
            throw new UserEmailAlreadyVerifiedException();
        }

        $user->sendEmailVerificationNotification();
    }

    /**
     * {@inheritDoc}
     */
    public function verify(EmailVerify $data): void
    {
        $user = $this->userService->findOrFail($data->id);

        if (! hash_equals($data->id, (string)$user->id)) {
            throw new InvalidEmailVerifyDataException();
        }

        if (! hash_equals($data->hash, sha1($user->getEmailForVerification()))) {
            throw new InvalidEmailVerifyDataException();
        }

        if ($user->hasVerifiedEmail()) {
            return;
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }
    }
}
