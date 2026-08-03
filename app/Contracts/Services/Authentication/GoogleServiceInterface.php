<?php

namespace App\Contracts\Services\Authentication;

use App\DTOs\Authentication\{
    GoogleAuthenticationResult,
    GoogleAuthenticationAttempt,
};
use App\Exceptions\Authentication\Google\{
    InvalidIdTokenException,
    UnverifiedGoogleEmailException,
};

interface GoogleServiceInterface
{
    /**
     * Verify the Google ID token, find-or-create the local user, and
     * issue a platform API token — mirrors what AuthenticationService
     * returns for a normal email/password login.
     *
     * @throws InvalidIdTokenException
     * @throws UnverifiedGoogleEmailException
     */
    public function login(GoogleAuthenticationAttempt $data): GoogleAuthenticationResult;
}
