<?php

namespace App\Repositories\Authentication;

use App\Models\User;
use Illuminate\Contracts\Auth\Guard;
use App\Contracts\Repositories\Authentication\AuthContextRepositoryInterface;

class AuthContextRepository implements AuthContextRepositoryInterface
{
    /**
     * Create a new repository instance.
     *
     * @param Guard $auth
     * @return void
     */
    public function __construct(
        private readonly Guard $auth,
    ) {
        //
    }

    /**
     * {@inheritDoc}
     */
    public function id(): ?int
    {
        $id = $this->auth->id();

        return $id ? (int) $id : null;
    }

    /**
     * {@inheritDoc}
     */
    public function user(): ?User
    {
        /** @var User */
        return $this->auth->user();
    }

    /**
     * {@inheritDoc}
     */
    public function check(): bool
    {
        return $this->auth->check();
    }
}
