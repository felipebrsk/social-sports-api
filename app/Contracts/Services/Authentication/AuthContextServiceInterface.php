<?php

namespace App\Contracts\Services\Authentication;

use App\Models\User;
use Illuminate\Auth\AuthenticationException;

interface AuthContextServiceInterface
{
    /**
     * Get the ID of the currently authenticated user.
     *
     * @return int
     * @throws AuthenticationException
     */
    public function id(): int;

    /**
     * Get the currently authenticated user.
     *
     * @return User
     * @throws AuthenticationException
     */
    public function user(): User;

    /**
     * Check if user is currently authenticated.
     *
     * @return bool
     */
    public function check(): bool;
}
