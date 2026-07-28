<?php

namespace Tests\Traits\Dummy;

use App\Models\Venue;
use Illuminate\Database\Eloquent\Collection;

trait HasDummyVenue
{
    /**
     * Create a generic venue.
     *
     * @param  array<string, mixed>  $data
     */
    public function createDummyVenue(array $data = []): Venue
    {
        return Venue::factory()->create($data);
    }

    /**
     * Create multiple generic venues.
     *
     * @param  array<string, mixed>  $data
     * @return Collection<int, Venue>
     */
    public function createDummyVenues(int $count, array $data = []): Collection
    {
        return Venue::factory($count)->create($data);
    }
}
