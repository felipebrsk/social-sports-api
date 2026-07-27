<?php

namespace Tests\Unit\Repositories;

use Mockery;
use Tests\TestCase;
use App\Models\User;
use Mockery\MockInterface;
use App\Repositories\UserRepository;

class UserRepositoryTest extends TestCase
{
    /**
     * The model mock.
     *
     * @var User&MockInterface
     */
    private User&MockInterface $model;

    /**
     * The repository instance.
     *
     * @var UserRepository
     */
    private UserRepository $repository;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->model = Mockery::mock(User::class);

        $this->repository = new UserRepository(
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
        $this->assertInstanceOf(UserRepository::class, $this->repository);
    }

    /**
     * Test if the constructor sets the correct model instance.
     *
     * @return void
     */
    public function test_if_constructor_sets_the_correct_model(): void
    {
        $actualModel = $this->getProtectedProperty($this->repository, 'model');

        $this->assertInstanceOf(User::class, $actualModel);
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
            'email',
            'blocked',
            'created_at',
            'updated_at',
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
            'email',
            'blocked',
            'created_at',
            'updated_at',
        ];

        $this->assertEquals($expectedSorts, $this->repository->getAllowedSorts());
    }
}
