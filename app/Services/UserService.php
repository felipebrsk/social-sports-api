<?php

namespace App\Services;

use App\Models\User;
use App\Contracts\Services\UserServiceInterface;
use App\Contracts\Repositories\UserRepositoryInterface;

/**
 * @extends AbstractService<User, UserRepositoryInterface>
 */
class UserService extends AbstractService implements UserServiceInterface
{
    /**
     * Create a new service instance.
     *
     * @param UserRepositoryInterface $repository
     * @return void
     */
    public function __construct(UserRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}
