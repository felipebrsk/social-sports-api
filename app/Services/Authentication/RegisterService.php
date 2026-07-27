<?php

namespace App\Services\Authentication;

use App\Contracts\Services\UserServiceInterface;
use App\Contracts\Services\Authentication\RegisterServiceInterface;
use App\Contracts\Repositories\Authentication\AuthRepositoryInterface;
use App\DTOs\Authentication\{
    RegisterUser,
    RegisterResult,
};

class RegisterService implements RegisterServiceInterface
{
    /**
     * Create a new service instance.
     *
     * @param UserServiceInterface $userService
     * @return void
     */
    public function __construct(
        private readonly UserServiceInterface $userService,
        private readonly AuthRepositoryInterface $authRepository,
    ) {
        //
    }

    /**
     * {@inheritdoc}
     */
    public function register(RegisterUser $data): RegisterResult
    {
        $user = $this->userService->create([
            'name' => $data->name,
            'email' => $data->email,
            'password' => $data->password,
        ]);

        $user->sendEmailVerificationNotification();

        $user->profile()->create();

        $token = $this->authRepository->loginByUser($user);

        $result = new RegisterResult(
            'Usuário cadastrado com sucesso!',
            $token,
        );

        return $result;
    }
}
