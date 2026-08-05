<?php

namespace Tests\Unit\Services;

use Mockery;
use Tests\TestCase;
use App\Models\Venue;
use Mockery\MockInterface;
use App\Services\VenueService;
use App\Filters\Venue\VenueSearchFilter;
use Illuminate\Database\Eloquent\Collection;
use App\Filters\Venue\CalculateDistanceFilter;
use App\Contracts\Repositories\VenueRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class VenueServiceTest extends TestCase
{
    /**
     * The repository mock.
     *
     * @var VenueRepositoryInterface&MockInterface
     */
    private VenueRepositoryInterface&MockInterface $repository;

    /**
     * The service instance.
     *
     * @var VenueService
     */
    private VenueService $service;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = Mockery::mock(VenueRepositoryInterface::class);

        $this->service = new VenueService($this->repository);
    }

    /**
     * Test if the service is instantiated correctly.
     *
     * @return void
     */
    public function test_if_service_is_instantiated_correctly(): void
    {
        $this->assertInstanceOf(VenueService::class, $this->service);
    }

    /**
     * Test searchVenues correctly builds query and returns collection of venues.
     *
     * @return void
     */
    public function test_search_venues_applies_select_relations_counts_and_criteria(): void
    {
        $params = [
            'per_page' => 15,
            'city' => 'Salvador',
        ];

        /** @var Venue&MockInterface $venue */
        $venue = Mockery::mock(Venue::class);

        $expectedCollection = new LengthAwarePaginator([$venue], 1, 15);

        $this->repository
            ->shouldReceive('select')
            ->once()
            ->with([
                'id',
                'name',
                'verified',
                'featured',
                'neighborhood',
            ])->andReturnSelf();
        $this->repository
            ->shouldReceive('withRelations')
            ->once()
            ->with([
                'sports:id,name,icon',
            ])->andReturnSelf();
        $this->repository
            ->shouldReceive('withCount')
            ->once()
            ->with(Mockery::on(function (array $counts) {
                return isset($counts[0]) && $counts[0] === 'gameSessions'
                    && isset($counts['gameSessions as upcoming_games_count'])
                    && is_callable($counts['gameSessions as upcoming_games_count'])
                    && isset($counts['gameSessions as ongoing_games_count'])
                    && is_callable($counts['gameSessions as ongoing_games_count']);
            }))->andReturnSelf();
        $this->repository
            ->shouldReceive('withCriteria')
            ->once()
            ->with(Mockery::type(VenueSearchFilter::class))
            ->andReturnSelf();
        $this->repository
            ->shouldReceive('paginate')
            ->once()
            ->with($params['per_page'])
            ->andReturn($expectedCollection);

        $result = $this->service->searchVenues($params);

        $this->assertSame($expectedCollection, $result);
    }

    /**
     * Test searchVenues extracts filter_by key when nested in params.
     *
     * @return void
     */
    public function test_search_venues_extracts_nested_filter_by_params(): void
    {
        $params = [
            'filter_by' => [
                'state' => 'BA',
                'city' => 'Feira de Santana',
            ],
        ];

        $expectedCollection = new LengthAwarePaginator([], 0, 15);

        $this->repository
            ->shouldReceive('select')
            ->once()
            ->andReturnSelf();
        $this->repository
            ->shouldReceive('withRelations')
            ->once()
            ->andReturnSelf();
        $this->repository
            ->shouldReceive('withCount')
            ->once()
            ->andReturnSelf();
        $this->repository
            ->shouldReceive('withCriteria')
            ->once()
            ->with(Mockery::on(function (VenueSearchFilter $filter) {
                return true;
            }))->andReturnSelf();
        $this->repository
            ->shouldReceive('paginate')
            ->once()
            ->with(15)
            ->andReturn($expectedCollection);

        $result = $this->service->searchVenues($params);

        $this->assertSame($expectedCollection, $result);
    }

    /**
     * Test getVenueDetails builds eager loading for game sessions without distance filter when coordinates are missing.
     *
     * @return void
     */
    public function test_get_venue_details_without_coordinates(): void
    {
        $venueId = 10;

        /** @var Venue&MockInterface $venue */
        $venue = Mockery::mock(Venue::class);
        $venue
            ->shouldReceive('getAttribute')
            ->with('id')
            ->andReturn($venueId);

        $this->repository
            ->shouldReceive('select')
            ->once()
            ->with([
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
            ])->andReturnSelf();
        $this->repository
            ->shouldReceive('withRelations')
            ->once()
            ->with(Mockery::on(function (array $relations) {
                return isset($relations[0]) && $relations[0] === 'sports:id,name,icon'
                    && isset($relations['gameSessions'])
                    && is_callable($relations['gameSessions']);
            }))->andReturnSelf();

        $this->repository->shouldNotReceive('withCriteria');
        $this->repository
            ->shouldReceive('findOrFail')
            ->once()
            ->with($venueId)
            ->andReturn($venue);

        $result = $this->service->getVenueDetails($venueId);

        $this->assertSame($venue, $result);
    }

    /**
     * Test getVenueDetails applies CalculateDistanceFilter when coordinates are present.
     *
     * @return void
     */
    public function test_get_venue_details_applies_distance_filter_when_coordinates_provided(): void
    {
        $venueId = 5;
        $latitude = -23.550520;
        $longitude = -46.633308;

        $params = [
            'latitude' => $latitude,
            'longitude' => $longitude,
        ];

        /** @var Venue&MockInterface $venue */
        $venue = Mockery::mock(Venue::class);
        $venue
            ->shouldReceive('getAttribute')
            ->with('id')
            ->andReturn($venueId);

        $this->repository
            ->shouldReceive('select')
            ->once()
            ->andReturnSelf();
        $this->repository
            ->shouldReceive('withRelations')
            ->once()
            ->andReturnSelf();
        $this->repository
            ->shouldReceive('withCriteria')
            ->once()
            ->with(Mockery::type(CalculateDistanceFilter::class))
            ->andReturnSelf();
        $this->repository
            ->shouldReceive('findOrFail')
            ->once()
            ->with($venueId)
            ->andReturn($venue);

        $result = $this->service->getVenueDetails($venueId, $params);

        $this->assertSame($venue, $result);
    }
}
