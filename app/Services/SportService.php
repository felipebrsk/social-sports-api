<?php

namespace App\Services;

use App\Models\Sport;
use Illuminate\Database\Eloquent\Collection;
use App\Contracts\Services\SportServiceInterface;
use App\Contracts\Repositories\SportRepositoryInterface;

/**
 * @extends AbstractService<Sport, SportRepositoryInterface>
 */
class SportService extends AbstractService implements SportServiceInterface
{
    /**
     * Create a new service instance.
     *
     * @param SportRepositoryInterface $repository
     * @return void
     */
    public function __construct(SportRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }

    /**
     * {@inheritdoc}
     */
    public function all(): Collection
    {
        return $this->repository->select([
            'id',
            'name',
            'icon',
        ])->all();
    }
}
