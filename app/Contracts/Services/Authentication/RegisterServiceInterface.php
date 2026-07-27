<?php

namespace App\Contracts\Services\Authentication;

use App\DTOs\Authentication\{
    RegisterUser,
    RegisterResult,
};

interface RegisterServiceInterface
{
    /**
     * Register a new user on platform.
     *
     * @param RegisterUser $data
     * @return RegisterResult
     */
    public function register(RegisterUser $data): RegisterResult;
}
