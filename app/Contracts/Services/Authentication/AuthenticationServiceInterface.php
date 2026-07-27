<?php

namespace App\Contracts\Services\Authentication;

use App\Exceptions\Authentication\InvalidCredentialsException;
use App\DTOs\Authentication\{
    LoginResult,
    LoginAttempt,
};

interface AuthenticationServiceInterface
{
    /**
     * Authenticates user with given credentials.
     *
     * @param LoginAttempt $data
     * @return LoginResult
     * @throws InvalidCredentialsException
     */
    public function login(LoginAttempt $data): LoginResult;
}
