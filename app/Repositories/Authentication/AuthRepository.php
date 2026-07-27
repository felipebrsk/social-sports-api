<?php

namespace App\Repositories\Authentication;

use Tymon\JWTAuth\JWTGuard;
use App\DTOs\Authentication\LoginAttempt;
use Illuminate\Contracts\Auth\Factory as AuthFactory;
use App\Contracts\Repositories\Authentication\AuthRepositoryInterface;
use App\Models\User;

class AuthRepository implements AuthRepositoryInterface
{
    /**
     * The guard instance.
     */
    private readonly JWTGuard $guard;

    /**
     * Create a new repository instance.
     *
     * @return void
     */
    public function __construct(
        private readonly AuthFactory $auth,
    ) {
        /** @var JWTGuard $guard */
        $guard = $this->auth->guard('api');

        $this->guard = $guard;
    }

    /**
     * {@inheritDoc}
     */
    public function attempt(LoginAttempt $loginAttempt): string
    {
        return (string) $this->guard->attempt([
            'email' => $loginAttempt->email,
            'password' => $loginAttempt->password,
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function loginByUser(User $user): string
    {
        return $this->guard->login($user);
    }
}
