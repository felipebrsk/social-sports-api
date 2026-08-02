<?php

namespace App\Contracts\Services;

use App\Models\Venue;
use Illuminate\Database\Eloquent\Collection;

/**
 * @extends AbstractServiceInterface<Venue>
 */
interface VenueServiceInterface extends AbstractServiceInterface
{
    /**
     * Search venues applying GPS, city, sport and text search.
     *
     * @param array<string, mixed> $params
     * @return Collection<int, Venue>
     */
    public function searchVenues(array $params): Collection;

    /**
     * Get the venue details, including distance calculation and coords.
     *
     * @param int $id
     * @param array<string, string|float> $params
     * @return Venue
     */
    public function getVenueDetails(int $id, array $params = []): Venue;
}
