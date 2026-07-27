<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\AbstractRepository;
use App\Contracts\Repositories\UserRepositoryInterface;

/**
 * @extends AbstractRepository<User>
 */
class UserRepository extends AbstractRepository implements UserRepositoryInterface
{
    /**
     * {@inheritDoc}
     */
    protected array $allowedFilters = [
        'id',
        'name',
        'email',
        'blocked',
        'created_at',
        'updated_at',
    ];

    /**
     * {@inheritDoc}
     */
    protected array $allowedSorts = [
        'id',
        'name',
        'email',
        'blocked',
        'created_at',
        'updated_at',
    ];

    /**
     * Create a new repository instance.
     *
     * @param User $model
     * @return void
     */
    public function __construct(User $model)
    {
        parent::__construct($model);
    }
}
