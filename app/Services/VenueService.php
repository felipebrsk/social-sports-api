<?php

namespace App\Services;

use App\Enums\GameSessionRequestStatusEnum;
use App\Models\Venue;
use Illuminate\Support\Carbon;
use App\Enums\GameSessionStatusEnum;
use App\Contracts\Services\VenueServiceInterface;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Contracts\Repositories\VenueRepositoryInterface;
use App\Filters\Venue\{
    VenueSearchFilter,
    CalculateDistanceFilter,
};
use Illuminate\Database\Eloquent\{
    Builder,
    Collection,
};

/**
 * @extends AbstractService<Venue, VenueRepositoryInterface>
 */
class VenueService extends AbstractService implements VenueServiceInterface
{
    /**
     * Create a new service instance.
     *
     * @param VenueRepositoryInterface $repository
     * @return void
     */
    public function __construct(VenueRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }

    /**
     * {@inheritDoc}
     */
    public function searchVenues(array $params): Collection
    {
        /** @var array<string, float|int|string|null> $filters */
        $filters = $params['filter_by'] ?? $params;

        $now = Carbon::now()->toDateTimeString();

        $invalidStatusIds = [
            GameSessionStatusEnum::FINISHED->value,
            GameSessionStatusEnum::CANCELLED->value,
        ];

        return $this->repository->select([
            'id',
            'name',
            'verified',
            'featured',
            'neighborhood',
        ])->withRelations([
            'sports:id,name,icon',
        ])->withCount([
            'gameSessions',
            'gameSessions as upcoming_games_count' => function (Builder $query) use ($now, $invalidStatusIds) {
                $query->where('start_time', '>', $now)
                    ->whereNotIn('game_session_status_id', $invalidStatusIds);
            },
            'gameSessions as ongoing_games_count' => function (Builder $query) use ($now, $invalidStatusIds) {
                $query->where('start_time', '<=', $now)
                    ->where('end_time', '>=', $now)
                    ->whereNotIn('game_session_status_id', $invalidStatusIds);
            },
        ])->withCriteria(new VenueSearchFilter($filters))->all();
    }

    /**
     * {@inheritDoc}
     */
    public function getVenueDetails(int $id, array $params = []): Venue
    {
        $now = Carbon::now()->toDateTimeString();

        $invalidStatusIds = [
            GameSessionStatusEnum::FINISHED->value,
            GameSessionStatusEnum::CANCELLED->value,
        ];

        $this->repository->select([
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
        ])->withRelations([
            'sports:id,name,icon',
            'gameSessions' => function (HasMany $query) use ($now, $invalidStatusIds) {
                $query->select([
                    'id',
                    'end_time',
                    'venue_id',
                    'sport_id',
                    'start_time',
                    'creator_id',
                    'description',
                    'max_players',
                    'skill_level_id',
                    'external_players_count',
                ])->withCount([
                    'requests as approved_requests_count' => function (Builder $query) {
                        $query->where('game_session_request_status_id', GameSessionRequestStatusEnum::APPROVED->value);
                    },
                ])->with([
                    'creator:id,name',
                    'creator.profile:id,avatar,user_id',
                    'skillLevel:id,name',
                    'sport:id,name,icon',
                ])->where('end_time', '>', $now)
                    ->whereNotIn('game_session_status_id', $invalidStatusIds)
                    ->orderBy('featured', 'desc')
                    ->orderBy('start_time', 'asc')
                    ->limit(10);
            }
        ]);

        if (! empty($params['latitude']) && ! empty($params['longitude'])) {
            $this->repository->withCriteria(
                new CalculateDistanceFilter(
                    (float) $params['latitude'],
                    (float) $params['longitude'],
                ),
            );
        }

        return $this->repository->findOrFail($id);
    }
}
