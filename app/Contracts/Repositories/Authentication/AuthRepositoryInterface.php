<?php

namespace App\Contracts\Repositories\Authentication;

use App\DTOs\Authentication\LoginAttempt;

interface AuthRepositoryInterface
{
    /**
     * Authenticate user by credentials.
     *
     * @param LoginAttempt $loginAttempt
     * @return string
     */
    public function attempt(LoginAttempt $loginAttempt): string;
}
