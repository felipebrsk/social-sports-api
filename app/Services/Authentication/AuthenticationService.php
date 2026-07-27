<?php

namespace App\Services\Authentication;

use App\Events\Authentication\LoginSuccessful;
use App\Contracts\Repositories\Authentication\AuthRepositoryInterface;
use App\Contracts\Services\Authentication\AuthenticationServiceInterface;
use App\Contracts\Services\{
    UserServiceInterface,
    HashServiceInterface,
};
use App\DTOs\Authentication\{
    LoginResult,
    LoginAttempt,
};
use App\Exceptions\Authentication\{
    UserIsBlockedException,
    InvalidCredentialsException,
};

class AuthenticationService implements AuthenticationServiceInterface
{
    /**
     * Create a new service instance.
     *
     * @return void
     */
    public function __construct(
        private readonly UserServiceInterface $userService,
        private readonly HashServiceInterface $hashingService,
        private readonly AuthRepositoryInterface $authRepository,
    ) {
        //
    }

    /**
     * {@inheritDoc}
     */
    public function login(LoginAttempt $data): LoginResult
    {
        $user = $this->userService->findBy('email', $data->email);

        if (! $user) {
            throw new InvalidCredentialsException();
        }

        if (! $this->hashingService->check($data->password, $user->getAuthPassword())) {
            throw new InvalidCredentialsException();
        }

        if ($user->blocked) {
            throw new UserIsBlockedException();
        }

        $token = $this->authRepository->attempt($data);

        LoginSuccessful::dispatch($user);

        return new LoginResult('Usuário autenticado com sucesso!', $token);
    }
}
