<?php

namespace Tests\Unit\Repositories;

use Mockery;
use Tests\TestCase;
use App\Models\GameSession;
use Mockery\MockInterface;
use App\Repositories\GameSessionRepository;

class GameSessionRepositoryTest extends TestCase
{
    /**
     * The model mock.
     *
     * @var GameSession&MockInterface
     */
    private GameSession&MockInterface $model;

    /**
     * The repository instance.
     *
     * @var GameSessionRepository
     */
    private GameSessionRepository $repository;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->model = Mockery::mock(GameSession::class);

        $this->repository = new GameSessionRepository(
            $this->model,
        );
    }

    /**
     * Test if the repository is instantiated correctly with the given model.
     *
     * @return void
     */
    public function test_if_repository_is_instantiated_correctly(): void
    {
        $this->assertInstanceOf(GameSessionRepository::class, $this->repository);
    }

    /**
     * Test if the constructor sets the correct model instance.
     *
     * @return void
     */
    public function test_if_constructor_sets_the_correct_model(): void
    {
        $actualModel = $this->getProtectedProperty($this->repository, 'model');

        $this->assertInstanceOf(GameSession::class, $actualModel);
    }

    /**
     * Test if the repository has the correct allowed filters configured.
     *
     * @return void
     */
    public function test_if_has_correct_allowed_filters(): void
    {
        $expectedFilters = [
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

        $this->assertEquals($expectedFilters, $this->repository->getAllowedFilters());
    }

    /**
     * Test if the repository has the correct allowed sorts configured.
     *
     * @return void
     */
    public function test_if_has_correct_allowed_sorts(): void
    {
        $expectedSorts = [
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

        $this->assertEquals($expectedSorts, $this->repository->getAllowedSorts());
    }
}
