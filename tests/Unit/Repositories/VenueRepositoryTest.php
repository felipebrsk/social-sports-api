<?php

namespace Tests\Unit\Repositories;

use Mockery;
use Tests\TestCase;
use App\Models\Venue;
use Mockery\MockInterface;
use App\Repositories\VenueRepository;

class VenueRepositoryTest extends TestCase
{
    /**
     * The model mock.
     *
     * @var Venue&MockInterface
     */
    private Venue&MockInterface $model;

    /**
     * The repository instance.
     *
     * @var VenueRepository
     */
    private VenueRepository $repository;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->model = Mockery::mock(Venue::class);

        $this->repository = new VenueRepository(
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
        $this->assertInstanceOf(VenueRepository::class, $this->repository);
    }

    /**
     * Test if the constructor sets the correct model instance.
     *
     * @return void
     */
    public function test_if_constructor_sets_the_correct_model(): void
    {
        $actualModel = $this->getProtectedProperty($this->repository, 'model');

        $this->assertInstanceOf(Venue::class, $actualModel);
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
            'name',
            'city',
            'state',
            'address',
            'latitude',
            'verified',
            'featured',
            'sport_id',
            'longitude',
            'neighborhood',
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
            'name',
            'city',
            'state',
            'address',
            'latitude',
            'verified',
            'featured',
            'longitude',
            'neighborhood',
            'created_at',
        ];

        $this->assertEquals($expectedSorts, $this->repository->getAllowedSorts());
    }
}
