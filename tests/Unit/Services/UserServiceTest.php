<?php

namespace Tests\Unit\Services;

use Mockery;
use Tests\TestCase;
use Mockery\MockInterface;
use App\Services\UserService;
use App\Contracts\Repositories\UserRepositoryInterface;

class UserServiceTest extends TestCase
{
    /**
     * The repository mock.
     *
     * @var UserRepositoryInterface&MockInterface
     */
    private UserRepositoryInterface&MockInterface $repository;

    /**
     * The service instance.
     *
     * @var UserService
     */
    private UserService $service;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = Mockery::mock(UserRepositoryInterface::class);

        $this->service = new UserService($this->repository);
    }

    /**
     * Test if the service is instantiated correctly.
     *
     * @return void
     */
    public function test_if_service_is_instantiated_correctly(): void
    {
        $this->assertInstanceOf(UserService::class, $this->service);
    }
}
