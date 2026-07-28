<?php

namespace Tests\Unit\Services;

use Mockery;
use Tests\TestCase;
use App\Models\Sport;
use Mockery\MockInterface;
use App\Services\SportService;
use App\Contracts\Services\SportServiceInterface;
use App\Contracts\Repositories\SportRepositoryInterface;
use App\Enums\GameSessionStatusEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

class SportServiceTest extends TestCase
{
    /**
     * The sport repository mock.
     *
     * @var SportRepositoryInterface&MockInterface
     */
    private SportRepositoryInterface&MockInterface $sportRepository;

    /**
     * The sport service.
     *
     * @var SportServiceInterface
     */
    private SportServiceInterface $sportService;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->sportRepository = Mockery::mock(SportRepositoryInterface::class);

        $this->sportService = new SportService(
            $this->sportRepository,
        );
    }

    /**
     * Test if the service is instantiated correctly.
     *
     * @return void
     */
    public function test_if_service_is_instantiated_correctly(): void
    {
        $this->assertInstanceOf(SportService::class, $this->sportService);
    }

    /**
     * Test if can get all sports for users correctly.
     *
     * @return void
     */
    public function test_if_can_get_all_sports_for_users_correctly(): void
    {
        $now = Carbon::now()->toDateTimeString();

        /** @var Collection<int, Sport>&MockInterface $expected */
        $expected = Mockery::mock(Collection::class);

        $invalidStatusIds = [
            GameSessionStatusEnum::FINISHED->value,
            GameSessionStatusEnum::CANCELLED->value,
        ];

        $this->sportRepository
            ->shouldReceive('select')
            ->once()
            ->with([
                'id',
                'name',
                'icon',
            ])->andReturnSelf();
        $this->sportRepository
            ->shouldReceive('withCount')
            ->once()
            ->with(Mockery::on(function (array $args) use ($now, $invalidStatusIds) {
                if (($args[0] ?? null) !== 'venues') {
                    return false;
                }

                if (! isset($args['gameSessions as upcoming_games_count']) || ! $args['gameSessions as upcoming_games_count'] instanceof \Closure) {
                    return false;
                }

                if (! isset($args['gameSessions as ongoing_games_count']) || ! $args['gameSessions as ongoing_games_count'] instanceof \Closure) {
                    return false;
                }

                $upcomingQueryMock = Mockery::mock(Builder::class);
                $upcomingQueryMock
                    ->shouldReceive('where')
                    ->once()
                    ->with('start_time', '>', $now)
                    ->andReturnSelf();
                $upcomingQueryMock
                    ->shouldReceive('whereNotIn')
                    ->once()
                    ->with('game_session_status_id', $invalidStatusIds)
                    ->andReturnSelf();

                $args['gameSessions as upcoming_games_count']($upcomingQueryMock);

                $ongoingQueryMock = Mockery::mock(Builder::class);
                $ongoingQueryMock
                    ->shouldReceive('where')
                    ->once()
                    ->with('start_time', '<=', $now)
                    ->andReturnSelf();
                $ongoingQueryMock
                    ->shouldReceive('where')
                    ->once()
                    ->with('end_time', '>=', $now)
                    ->andReturnSelf();
                $ongoingQueryMock
                    ->shouldReceive('whereNotIn')
                    ->once()
                    ->with('game_session_status_id', $invalidStatusIds)
                    ->andReturnSelf();

                $args['gameSessions as ongoing_games_count']($ongoingQueryMock);

                return true;
            }))->andReturnSelf();
        $this->sportRepository
            ->shouldReceive('all')
            ->once()
            ->withNoArgs()
            ->andReturn($expected);

        $result = $this->sportService->all();

        $this->assertEquals($expected, $result);
    }
}
