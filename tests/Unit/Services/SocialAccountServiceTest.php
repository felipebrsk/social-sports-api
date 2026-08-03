<?php

namespace Tests\Unit\Services;

use Mockery;
use Tests\TestCase;
use Mockery\MockInterface;
use App\Services\SocialAccountService;
use App\Contracts\Repositories\SocialAccountRepositoryInterface;

class SocialAccountServiceTest extends TestCase
{
    /**
     * The repository mock.
     *
     * @var SocialAccountRepositoryInterface&MockInterface
     */
    private SocialAccountRepositoryInterface&MockInterface $repository;

    /**
     * The service instance.
     *
     * @var SocialAccountService
     */
    private SocialAccountService $service;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = Mockery::mock(SocialAccountRepositoryInterface::class);

        $this->service = new SocialAccountService($this->repository);
    }

    /**
     * Test if the service is instantiated correctly.
     *
     * @return void
     */
    public function test_if_service_is_instantiated_correctly(): void
    {
        $this->assertInstanceOf(SocialAccountService::class, $this->service);
    }
}
