<?php

namespace Tests\Unit\Repositories;

use Mockery;
use Tests\TestCase;
use App\Models\Sport;
use Mockery\MockInterface;
use App\Repositories\SportRepository;

class SportRepositoryTest extends TestCase
{
    /**
     * The model mock.
     *
     * @var Sport&MockInterface
     */
    private Sport&MockInterface $model;

    /**
     * The repository instance.
     *
     * @var SportRepository
     */
    private SportRepository $repository;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->model = Mockery::mock(Sport::class);

        $this->repository = new SportRepository(
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
        $this->assertInstanceOf(SportRepository::class, $this->repository);
    }

    /**
     * Test if the constructor sets the correct model instance.
     *
     * @return void
     */
    public function test_if_constructor_sets_the_correct_model(): void
    {
        $actualModel = $this->getProtectedProperty($this->repository, 'model');

        $this->assertInstanceOf(Sport::class, $actualModel);
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
        ];

        $this->assertEquals($expectedSorts, $this->repository->getAllowedSorts());
    }
}
