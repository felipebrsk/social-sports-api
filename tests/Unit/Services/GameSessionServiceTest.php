<?php

namespace Tests\Unit\Services;

use Mockery;
use Tests\TestCase;
use Mockery\MockInterface;
use App\Models\GameSession;
use App\Services\GameSessionService;
use App\DTOs\GameSession\GameSessionDetails;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Filters\GameSession\GameSessionSearchFilter;
use App\Contracts\Repositories\GameSessionRepositoryInterface;
use App\Contracts\Services\Authentication\AuthContextServiceInterface;

use function in_array;

class GameSessionServiceTest extends TestCase
{
    /**
     * The repository mock.
     *
     * @var GameSessionRepositoryInterface&MockInterface
     */
    private GameSessionRepositoryInterface&MockInterface $repository;

    /**
     * The auth context service mock.
     *
     * @var AuthContextServiceInterface&MockInterface
     */
    private AuthContextServiceInterface&MockInterface $authContextService;

    /**
     * The service instance.
     *
     * @var GameSessionService
     */
    private GameSessionService $service;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = Mockery::mock(GameSessionRepositoryInterface::class);
        $this->authContextService = Mockery::mock(AuthContextServiceInterface::class);

        $this->service = new GameSessionService(
            $this->repository,
            $this->authContextService,
        );
    }

    /**
     * Test if the service is instantiated correctly.
     *
     * @return void
     */
    public function test_if_service_is_instantiated_correctly(): void
    {
        $this->assertInstanceOf(GameSessionService::class, $this->service);
    }

    /**
     * Test getFeed correctly builds query and returns collection of game sessions.
     *
     * @return void
     */
    public function test_get_feed_applies_select_relations_counts_and_criteria(): void
    {
        $uid = 15;

        $params = [
            'per_page' => 15,
            'city' => 'Salvador',
        ];

        /** @var GameSession&MockInterface $venue */
        $venue = Mockery::mock(GameSession::class);

        $expectedCollection = new LengthAwarePaginator([$venue], 1, 15);

        $this->authContextService
            ->shouldReceive('id')
            ->once()
            ->withNoArgs()
            ->andReturn($uid);

        $this->repository
            ->shouldReceive('select')
            ->once()
            ->with([
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
            ])->andReturnSelf();
        $this->repository
            ->shouldReceive('withRelations')
            ->once()
            ->with([
                'venue:id,name,neighborhood,city,state',
                'sport:id,name,icon',
                'skillLevel:id,name',
                'status:id,name',
                'creator:id,name',
                'creator.profile:id,avatar,user_id',
            ])->andReturnSelf();
        $this->repository
            ->shouldReceive('withCount')
            ->once()
            ->with(Mockery::on(function (array $counts) {
                return isset($counts['requests as approved_requests_count']) && is_callable($counts['requests as approved_requests_count']);
            }))->andReturnSelf();
        $this->repository
            ->shouldReceive('withCriteria')
            ->once()
            ->with(Mockery::type(GameSessionSearchFilter::class))
            ->andReturnSelf();
        $this->repository
            ->shouldReceive('paginate')
            ->once()
            ->with($params['per_page'])
            ->andReturn($expectedCollection);

        $result = $this->service->getFeed($params);

        $this->assertSame($expectedCollection, $result);
    }

    /**
     * Test getDetails correctly builds query and returns correct game session.
     *
     * @return void
     */
    public function test_get_details_applies_select_relations_counts_and_criteria(): void
    {
        $id = 1;
        $authUserId = 10;
        $lat = -12.9714;
        $lng = -38.5014;

        $dto = new GameSessionDetails($id, $lat, $lng);

        /** @var GameSession&MockInterface $expectedGameSession */
        $expectedGameSession = Mockery::mock(GameSession::class);

        $this->authContextService
            ->shouldReceive('id')
            ->once()
            ->withNoArgs()
            ->andReturn($authUserId);

        $this->repository
            ->shouldReceive('select')
            ->once()
            ->with([
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
            ])->andReturnSelf();

        $this->repository
            ->shouldReceive('withRelations')
            ->once()
            ->with(Mockery::on(function (array $relations) {
                return isset($relations['requests'])
                    && is_callable($relations['requests'])
                    && in_array('venue:id,name,neighborhood,city,state,latitude,longitude,verified,featured', $relations, true)
                    && in_array('sport:id,name,icon', $relations, true)
                    && in_array('skillLevel:id,name', $relations, true)
                    && in_array('creator.profile', $relations, true)
                    && in_array('hostTeam:id,name,logo', $relations, true)
                    && in_array('visitorTeam:id,name,logo', $relations, true)
                    && in_array('status:id,name', $relations, true)
                    && in_array('socialLinks:id,url,linkable_id,linkable_type,social_network_id', $relations, true)
                    && in_array('socialLinks.socialNetwork:id,name,icon', $relations, true);
            }))->andReturnSelf();

        $this->repository
            ->shouldReceive('withCount')
            ->once()
            ->with(Mockery::on(function (array $counts) {
                return isset($counts['requests as approved_requests_count']) && is_callable($counts['requests as approved_requests_count']);
            }))->andReturnSelf();

        $this->repository
            ->shouldReceive('withCriteria')
            ->once()
            ->with(Mockery::type(\App\Filters\GameSession\GameSessionDetailsCriteria::class))
            ->andReturnSelf();

        $this->repository
            ->shouldReceive('findOrFail')
            ->once()
            ->with($id)
            ->andReturn($expectedGameSession);

        $result = $this->service->getDetails($id, $dto);

        $this->assertSame($expectedGameSession, $result);
    }
}
