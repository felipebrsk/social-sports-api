<?php

namespace Tests\Unit\Services;

use Mockery;
use Tests\TestCase;
use App\Models\Sport;
use Mockery\MockInterface;
use App\Services\SportService;
use Illuminate\Database\Eloquent\Collection;
use App\Contracts\Services\SportServiceInterface;
use App\Contracts\Repositories\SportRepositoryInterface;

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
        /** @var Collection<int, Sport>&MockInterface $expected */
        $expected = Mockery::mock(Collection::class);

        $this->sportRepository
            ->shouldReceive('select')
            ->once()
            ->with([
                'id',
                'name',
                'icon',
            ])->andReturnSelf();
        $this->sportRepository
            ->shouldReceive('all')
            ->once()
            ->withNoArgs()
            ->andReturn($expected);

        $result = $this->sportService->all();

        $this->assertEquals($expected, $result);
    }
}
