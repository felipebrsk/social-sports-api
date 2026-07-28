<?php

namespace App\Repositories;

use App\Models\Sport;
use App\Contracts\Repositories\SportRepositoryInterface;

/**
 * @extends AbstractRepository<Sport>
 */
class SportRepository extends AbstractRepository implements SportRepositoryInterface
{
    /**
     * {@inheritDoc}
     */
    protected array $allowedFilters = [
        'id',
        'name',
    ];

    /**
     * {@inheritDoc}
     */
    protected array $allowedSorts = [
        'id',
        'name',
    ];

    /**
     * Create a new repository instance.
     *
     * @param Sport $model
     * @return void
     */
    public function __construct(Sport $model)
    {
        parent::__construct($model);
    }
}
