<?php

namespace App\Filters\Venue;

use App\Models\Venue;
use Illuminate\Database\Eloquent\Builder;
use App\Contracts\Filters\CriterionFilterInterface;

class VenueSearchFilter implements CriterionFilterInterface
{
    /**
     * Create a new filter instance.
     *
     * @param array<string, mixed> $filters
     */
    public function __construct(
        private readonly array $filters,
    ) {
        //
    }

    /**
     * {@inheritDoc}
     *
     * @param Builder<Venue> $query
     */
    public function apply(Builder $query): Builder
    {
        if (! empty($this->filters['search'])) {
            $search = $this->filters['search'];

            $query->where(function (Builder $subQuery) use ($search) {
                $subQuery->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('address', 'LIKE', "%{$search}%")
                    ->orWhere('neighborhood', 'LIKE', "%{$search}%")
                    ->orWhere('city', 'LIKE', "%{$search}%");
            });
        }

        if (! empty($this->filters['sport_id'])) {
            $sportId = $this->filters['sport_id'];

            $query->whereHas('sports', function (Builder $query) use ($sportId) {
                return $query->where('sports.id', $sportId);
            });
        }

        if (! empty($this->filters['latitude']) && ! empty($this->filters['longitude'])) {
            $lat = (float) $this->filters['latitude'];
            $lng = (float) $this->filters['longitude'];
            $radiusKm = (float) ($this->filters['radius_km'] ?? 15.0);

            $query->withDistance($lat, $lng)
                ->withinRadius($radiusKm)
                ->orderBy('featured', 'desc')
                ->orderBy('verified', 'desc')
                ->orderBy('distance_in_km', 'asc');

            return $query;
        }

        if (! empty($this->filters['city'])) {
            $city = $this->filters['city'];

            $query->where('city', 'LIKE', "%{$city}%");

            if (! empty($this->filters['state'])) {
                $state = strtoupper((string) $this->filters['state']);

                $query->where('state', $state);
            }

            return $query->orderBy('featured', 'desc')
                ->orderBy('verified', 'desc')
                ->orderBy('name', 'asc');
        }

        return $query->orderBy('featured', 'desc')
            ->orderBy('verified', 'desc')
            ->latest();
    }
}
