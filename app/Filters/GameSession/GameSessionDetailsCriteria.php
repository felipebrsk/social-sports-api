<?php

namespace App\Filters\GameSession;

use App\Models\GameSession;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;
use App\Contracts\Filters\CriterionFilterInterface;

/**
 * @implements CriterionFilterInterface<GameSession>
 */
class GameSessionDetailsCriteria implements CriterionFilterInterface
{
    /**
     * Create a new criteria instance.
     *
     * @param int|null $authUserId
     * @param float|null $latitude
     * @param float|null $longitude
     */
    public function __construct(
        private readonly ?int $authUserId = null,
        private readonly ?float $latitude = null,
        private readonly ?float $longitude = null,
    ) {
        //
    }

    /**
     * {@inheritDoc}
     */
    public function apply(Builder $query): Builder
    {
        if ($this->authUserId) {
            $query->selectRaw(
                'CASE WHEN game_sessions.creator_id = ? THEN 1 ELSE 0 END AS is_organizer',
                [$this->authUserId],
            );

            $query->addSelect([
                'user_request_status_id' => DB::table('game_session_requests')
                    ->select('game_session_request_status_id')
                    ->whereColumn('game_session_requests.game_session_id', 'game_sessions.id')
                    ->where('game_session_requests.user_id', $this->authUserId)
                    ->limit(1),
            ]);
        }

        if ($this->latitude && $this->longitude) {
            $query->selectRaw(
                '( 6371 * acos( cos( radians(?) ) * cos( radians( (SELECT latitude FROM venues WHERE id = game_sessions.venue_id) ) )
                * cos( radians( (SELECT longitude FROM venues WHERE id = game_sessions.venue_id) ) - radians(?) ) + sin( radians(?) )
                * sin( radians( (SELECT latitude FROM venues WHERE id = game_sessions.venue_id) ) ) ) ) AS distance_in_km',
                [$this->latitude, $this->longitude, $this->latitude],
            );
        }

        return $query;
    }
}
