<?php

namespace App\Repositories;

use App\Models\Profile;
use App\Contracts\Repositories\ProfileRepositoryInterface;

/**
 * @extends AbstractRepository<Profile>
 */
class ProfileRepository extends AbstractRepository implements ProfileRepositoryInterface
{
    /**
     * {@inheritDoc}
     */
    protected array $allowedFilters = [];

    /**
     * {@inheritDoc}
     */
    protected array $allowedSorts = [];

    /**
     * Create a new repository instance.
     *
     * @param Profile $model
     * @return void
     */
    public function __construct(Profile $model)
    {
        parent::__construct($model);
    }
}
