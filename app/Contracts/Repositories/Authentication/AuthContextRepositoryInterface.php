<?php

namespace App\Contracts\Repositories\Authentication;

use App\Models\User;

interface AuthContextRepositoryInterface
{
    /**
     * Get the ID of the currently authenticated user.
     *
     * @return int|null
     */
    public function id(): ?int;

    /**
     * Get the currently authenticated user.
     *
     * @return User|null
     */
    public function user(): ?User;

    /**
     * Check if user is currently authenticated.
     *
     * @return bool
     */
    public function check(): bool;
}
