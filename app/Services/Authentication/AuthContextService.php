<?php

namespace App\Services\Authentication;

use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use App\Contracts\Services\Authentication\AuthContextServiceInterface;
use App\Contracts\Repositories\Authentication\AuthContextRepositoryInterface;

class AuthContextService implements AuthContextServiceInterface
{
    /**
     * Create a new service instance.
     *
     * @param AuthContextRepositoryInterface $authContextRepository
     * @return void
     */
    public function __construct(
        private readonly AuthContextRepositoryInterface $authContextRepository,
    ) {
        //
    }

    /**
     * {@inheritDoc}
     */
    public function id(): int
    {
        $this->assertAuthenticated();

        /** @var int */
        return $this->authContextRepository->id();
    }

    /**
     * {@inheritDoc}
     */
    public function user(): User
    {
        $this->assertAuthenticated();

        /** @var User */
        return $this->authContextRepository->user();
    }

    /**
     * {@inheritDoc}
     */
    public function check(): bool
    {
        return $this->authContextRepository->check();
    }

    /**
     * Assert user is authenticated.
     */
    public function assertAuthenticated(): void
    {
        if (! $this->check()) {
            throw new AuthenticationException('Usuário não autenticado.');
        }
    }
}
