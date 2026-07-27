<?php

namespace App\Repositories\Authentication;

use App\Models\User;
use Illuminate\Auth\Passwords\TokenRepositoryInterface;
use App\Contracts\Repositories\Authentication\ResetPasswordRepositoryInterface;

class ResetPasswordRepository implements ResetPasswordRepositoryInterface
{
    /**
     * Create new class instance.
     *
     * @param TokenRepositoryInterface $tokenRepository
     * @return void
     */
    public function __construct(
        private readonly TokenRepositoryInterface $tokenRepository,
    ) {
        //
    }

    /**
     * Create reset token.
     *
     * @param User $user
     * @return string
     */
    public function createResetToken(User $user): string
    {
        return $this->tokenRepository->create($user);
    }

    /**
     * Check if user recently created a token.
     *
     * @param User $user
     * @return bool
     */
    public function recentlyCreatedToken(User $user): bool
    {
        return $this->tokenRepository->recentlyCreatedToken($user);
    }

    /**
     * Check if exists a token for given user.
     *
     * @param User $user
     * @param string $token
     * @return bool
     */
    public function exists(User $user, string $token): bool
    {
        return $this->tokenRepository->exists($user, $token);
    }

    /**
     * Delete token for given user.
     *
     * @param User $user
     * @return void
     */
    public function delete(User $user): void
    {
        $this->tokenRepository->delete($user);
    }
}
