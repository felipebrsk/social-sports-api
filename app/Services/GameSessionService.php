<?php

namespace App\Services;

use App\Models\GameSession;
use Illuminate\Database\Eloquent\Builder;
use App\Enums\GameSessionRequestStatusEnum;
use App\DTOs\GameSession\GameSessionDetails;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Filters\GameSession\GameSessionSearchFilter;
use App\Contracts\Services\GameSessionServiceInterface;
use App\Filters\GameSession\GameSessionDetailsCriteria;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Contracts\Repositories\GameSessionRepositoryInterface;
use App\Contracts\Services\Authentication\AuthContextServiceInterface;

/**
 * @extends AbstractService<GameSession, GameSessionRepositoryInterface>
 */
class GameSessionService extends AbstractService implements GameSessionServiceInterface
{
    /**
     * Create a new service instance.
     *
     * @param GameSessionRepositoryInterface $repository
     * @param AuthContextServiceInterface $authContextService
     * @return void
     */
    public function __construct(
        GameSessionRepositoryInterface $repository,
        private readonly AuthContextServiceInterface $authContextService,
    ) {
        parent::__construct($repository);
    }

    /**
     * {@inheritDoc}
     */
    public function getFeed(array $params): LengthAwarePaginator
    {
        $authUserId = $this->authContextService->id();

        /** @var array<string, mixed> $filters */
        $filters = $params['filter_by'] ?? [];

        /** @var int $perPage */
        $perPage = $params['per_page'] ?? 15;

        return $this->repository->select([
            'game_sessions.id',
            'game_sessions.venue_id',
            'game_sessions.sport_id',
            'game_sessions.skill_level_id',
            'game_sessions.creator_id',
            'game_sessions.start_time',
            'game_sessions.end_time',
            'game_sessions.max_players',
            'game_sessions.external_players_count',
            'game_sessions.featured',
            'game_sessions.description',
            'game_sessions.host_team_id',
            'game_sessions.game_session_status_id',
        ])->withRelations([
            'venue:id,name,neighborhood,city,state',
            'sport:id,name,icon',
            'skillLevel:id,name',
            'status:id,name',
            'creator:id,name',
            'creator.profile:id,avatar,user_id',
        ])->withCount([
            'requests as approved_requests_count' => function (Builder $query) {
                $query->where('game_session_request_status_id', GameSessionRequestStatusEnum::APPROVED->value);
            },
        ])->withCriteria(new GameSessionSearchFilter($filters, $authUserId))->paginate($perPage);
    }

    /**
     * Get game session details.
     *
     * @param int $id
     * @param GameSessionDetails $data
     * @return GameSession
     */
    public function getDetails(int $id, GameSessionDetails $data): GameSession
    {
        $authUserId = $this->authContextService->id();

        return $this->repository->select([
            'id',
            'venue_id',
            'sport_id',
            'skill_level_id',
            'creator_id',
            'start_time',
            'end_time',
            'max_players',
            'external_players_count',
            'featured',
            'description',
            'host_team_id',
            'visitor_team_id',
            'game_session_status_id',
        ])->withRelations([
            'venue:id,name,neighborhood,city,state,latitude,longitude,verified,featured',
            'sport:id,name,icon',
            'skillLevel:id,name',
            'creator.profile',
            'hostTeam:id,name,logo',
            'visitorTeam:id,name,logo',
            'status:id,name',
            'socialLinks:id,url,linkable_id,linkable_type,social_network_id',
            'socialLinks.socialNetwork:id,name,icon',
            'requests' => function (HasMany $query) {
                $query->select([
                    'id',
                    'user_id',
                    'game_session_id',
                ])->with([
                    'user:id,name',
                    'user.profile:id,avatar,user_id',
                ])->where('game_session_request_status_id', GameSessionRequestStatusEnum::APPROVED->value);
            },
        ])->withCount([
            'requests as approved_requests_count' => function (Builder $query) {
                $query->where('game_session_request_status_id', GameSessionRequestStatusEnum::APPROVED->value);
            },
        ])->withCriteria(
            new GameSessionDetailsCriteria(
                $authUserId,
                $data->latitude,
                $data->longitude,
            ),
        )->findOrFail($id);
    }
}
