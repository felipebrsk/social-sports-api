<?php

namespace App\Repositories;

use App\Models\GameSession;
use App\Contracts\Repositories\GameSessionRepositoryInterface;

/**
 * @extends AbstractRepository<GameSession>
 */
class GameSessionRepository extends AbstractRepository implements GameSessionRepositoryInterface
{
    /**
     * {@inheritDoc}
     */
    protected array $allowedFilters = [
        'id',
        'featured',
        'end_time',
        'sport_id',
        'venue_id',
        'start_time',
        'creator_id',
        'max_players',
        'description',
        'host_team_id',
        'skill_level_id',
        'visitor_team_id',
        'external_players_count',
        'game_session_status_id',
    ];

    /**
     * {@inheritDoc}
     */
    protected array $allowedSorts = [
        'id',
        'featured',
        'end_time',
        'sport_id',
        'venue_id',
        'start_time',
        'creator_id',
        'max_players',
        'description',
        'host_team_id',
        'skill_level_id',
        'visitor_team_id',
        'external_players_count',
        'game_session_status_id',
    ];

    /**
     * Create a new repository instance.
     *
     * @param GameSession $model
     * @return void
     */
    public function __construct(GameSession $model)
    {
        parent::__construct($model);
    }
}
