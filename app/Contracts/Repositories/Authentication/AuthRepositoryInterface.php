<?php

namespace App\Contracts\Repositories\Authentication;

use App\DTOs\Authentication\LoginAttempt;
use App\Models\User;

interface AuthRepositoryInterface
{
    /**
     * Authenticate user by credentials.
     *
     * @param LoginAttempt $loginAttempt
     * @return string
     */
    public function attempt(LoginAttempt $loginAttempt): string;

    /**
     * Login by a given user instance.
     *
     * @param User $user
     * @return string
     */
    public function loginByUser(User $user): string;
}
