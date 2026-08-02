<?php

namespace App\Repositories;

use App\Models\Venue;
use App\Contracts\Repositories\VenueRepositoryInterface;

/**
 * @extends AbstractRepository<Venue>
 */
class VenueRepository extends AbstractRepository implements VenueRepositoryInterface
{
    /**
     * {@inheritDoc}
     */
    protected array $allowedFilters = [
        'id',
        'name',
        'city',
        'state',
        'address',
        'latitude',
        'verified',
        'featured',
        'sport_id',
        'longitude',
        'neighborhood',
    ];

    /**
     * {@inheritDoc}
     */
    protected array $allowedSorts = [
        'id',
        'name',
        'city',
        'state',
        'address',
        'latitude',
        'verified',
        'featured',
        'longitude',
        'neighborhood',
        'created_at',
    ];

    /**
     * Create a new repository instance.
     *
     * @param Venue $model
     * @return void
     */
    public function __construct(Venue $model)
    {
        parent::__construct($model);
    }
}
