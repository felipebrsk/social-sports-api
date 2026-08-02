<?php

namespace App\Filters\Venue;

use App\Models\Venue;
use Illuminate\Database\Eloquent\Builder;
use App\Contracts\Filters\CriterionFilterInterface;

/**
 * @implements CriterionFilterInterface<Venue>
 */
class CalculateDistanceFilter implements CriterionFilterInterface
{
    /**
     * Create a new filter instance.
     *
     * @param float $latitude
     * @param float $longitude
     * @return void
     */
    public function __construct(
        private readonly float $latitude,
        private readonly float $longitude,
    ) {
        //
    }

    /**
     * {@inheritDoc}
     */
    public function apply(Builder $query): Builder
    {
        return $query->withDistance($this->latitude, $this->longitude);
    }
}
