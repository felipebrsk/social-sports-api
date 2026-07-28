<?php

namespace Tests\Unit\Repositories;

use Mockery;
use Tests\TestCase;
use App\Models\Profile;
use Mockery\MockInterface;
use App\Repositories\ProfileRepository;

class ProfileRepositoryTest extends TestCase
{
    /**
     * The model mock.
     *
     * @var Profile&MockInterface
     */
    private Profile&MockInterface $model;

    /**
     * The repository instance.
     *
     * @var ProfileRepository
     */
    private ProfileRepository $repository;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->model = Mockery::mock(Profile::class);

        $this->repository = new ProfileRepository(
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
        $this->assertInstanceOf(ProfileRepository::class, $this->repository);
    }

    /**
     * Test if the constructor sets the correct model instance.
     *
     * @return void
     */
    public function test_if_constructor_sets_the_correct_model(): void
    {
        $actualModel = $this->getProtectedProperty($this->repository, 'model');

        $this->assertInstanceOf(Profile::class, $actualModel);
    }

    /**
     * Test if the repository has the correct allowed filters configured.
     *
     * @return void
     */
    public function test_if_has_correct_allowed_filters(): void
    {
        $expectedFilters = [];

        $this->assertEquals($expectedFilters, $this->repository->getAllowedFilters());
    }

    /**
     * Test if the repository has the correct allowed sorts configured.
     *
     * @return void
     */
    public function test_if_has_correct_allowed_sorts(): void
    {
        $expectedSorts = [];

        $this->assertEquals($expectedSorts, $this->repository->getAllowedSorts());
    }
}
