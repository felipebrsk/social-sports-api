<?php

namespace App\Filters\GameSession;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;
use App\Contracts\Filters\CriterionFilterInterface;
use App\Models\{
    Venue,
    GameSession,
};
use App\Enums\{
    GameSessionStatusEnum,
    GameSessionRequestStatusEnum,
};

/**
 * @implements CriterionFilterInterface<GameSession>
 */
class GameSessionSearchFilter implements CriterionFilterInterface
{
    /**
     * Create a new filter instance.
     *
     * @param array<string, mixed> $filters
     * @param int|null $authUserId
     */
    public function __construct(
        private readonly array $filters,
        private readonly ?int $authUserId = null,
    ) {
        //
    }

    /**
     * {@inheritDoc}
     */
    public function apply(Builder $query): Builder
    {
        $now = Carbon::now()->toDateTimeString();

        if ($this->authUserId) {
            $query->selectRaw(
                'CASE WHEN game_sessions.creator_id = ? THEN 1 ELSE 0 END AS is_organizer',
                [$this->authUserId],
            );

            $query->addSelect([
                'user_request_status_id' => DB::table('game_session_requests')
                    ->select('game_session_request_status_id AS user_request_status_id')
                    ->whereColumn('game_session_requests.game_session_id', 'game_sessions.id')
                    ->where('game_session_requests.user_id', $this->authUserId)
                    ->limit(1),
            ]);
        }

        $query->addSelect([
            'venue_featured' => Venue::select('featured')
                ->whereColumn('venues.id', 'game_sessions.venue_id')
                ->limit(1),
            'venue_verified' => Venue::select('verified')
                ->whereColumn('venues.id', 'game_sessions.venue_id')
                ->limit(1),
        ]);

        if (! empty($this->filters['sport_id'])) {
            $query->where('game_sessions.sport_id', $this->filters['sport_id']);
        }

        if (! empty($this->filters['skill_level_id'])) {
            $query->where('game_sessions.skill_level_id', $this->filters['skill_level_id']);
        }

        if (! empty($this->filters['date'])) {
            /** @var string $date */
            $date = $this->filters['date'];

            $query->whereDate('game_sessions.start_time', $date);
        }

        if (! empty($this->filters['only_available'])) {
            $query->whereRaw('(game_sessions.max_players - game_sessions.external_players_count) > (
                SELECT COUNT(*) 
                FROM game_session_requests 
                WHERE game_session_requests.game_session_id = game_sessions.id 
                AND game_session_requests.game_session_request_status_id = ?
            )', [GameSessionRequestStatusEnum::APPROVED->value]);
        }

        $invalidStatusIds = [
            GameSessionStatusEnum::FINISHED->value,
            GameSessionStatusEnum::CANCELLED->value,
        ];

        $query
            ->where('game_sessions.end_time', '>', $now)
            ->whereNotIn('game_sessions.game_session_status_id', $invalidStatusIds);

        if (! empty($this->filters['search']) && is_scalar($this->filters['search'])) {
            $search = (string) $this->filters['search'];

            $query->where(function (Builder $subQuery) use ($search) {
                $subQuery
                    ->where('game_sessions.description', 'LIKE', "%{$search}%")
                    ->orWhereHas('venue', function (Builder $venueQuery) use ($search) {
                        $venueQuery
                            ->where('name', 'LIKE', "%{$search}%")
                            ->orWhere('neighborhood', 'LIKE', "%{$search}%")
                            ->orWhere('city', 'LIKE', "%{$search}%")
                            ->orWhere('state', 'LIKE', "%{$search}%");
                    })->orWhereHas('sport', function (Builder $sportQuery) use ($search) {
                        $sportQuery->where('name', 'LIKE', "%{$search}%");
                    });
            });
        }

        $query
            ->orderBy('game_sessions.featured', 'desc')
            ->orderBy('venue_featured', 'desc')
            ->orderBy('venue_verified', 'desc');

        if (! empty($this->filters['latitude']) && ! empty($this->filters['longitude'])) {
            $lat = is_numeric($this->filters['latitude']) ? (float) $this->filters['latitude'] : 0.0;
            $lng = is_numeric($this->filters['longitude']) ? (float) $this->filters['longitude'] : 0.0;
            $radiusKm = isset($this->filters['radius_km']) && is_numeric($this->filters['radius_km'])
                ? (float) $this->filters['radius_km']
                : 15.0;

            $query->whereHas('venue', function (Builder $venueQuery) use ($lat, $lng, $radiusKm) {
                /** @var Builder<Venue> $venueQuery */
                $venueQuery->withinRadius($radiusKm, $lat, $lng);
            });

            $query->selectRaw(
                '( 6371 * acos( cos( radians(?) ) * cos( radians( (SELECT latitude FROM venues WHERE id = game_sessions.venue_id) ) )
                * cos( radians( (SELECT longitude FROM venues WHERE id = game_sessions.venue_id) ) - radians(?) ) + sin( radians(?) )
                * sin( radians( (SELECT latitude FROM venues WHERE id = game_sessions.venue_id) ) ) ) ) AS distance_in_km',
                [$lat, $lng, $lat],
            )->orderBy('distance_in_km', 'asc');
        }

        return $query
            ->orderBy('game_sessions.start_time', 'asc')
            ->orderBy('game_sessions.id', 'asc');
    }
}
