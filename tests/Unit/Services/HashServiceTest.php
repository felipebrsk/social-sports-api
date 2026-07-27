<?php

namespace Tests\Unit\Services;

use Mockery;
use Tests\TestCase;
use Mockery\MockInterface;
use App\Services\HashService;
use Illuminate\Contracts\Hashing\Hasher;
use App\Contracts\Services\HashServiceInterface;

class HashServiceTest extends TestCase
{
    /**
     * The hasher instance.
     *
     * @var Hasher&MockInterface
     */
    private Hasher&MockInterface $hasher;

    /**
     * The hash service.
     *
     * @var HashServiceInterface
     */
    private HashServiceInterface $hashService;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->hasher = Mockery::mock(Hasher::class);

        $this->hashService = new HashService(
            $this->hasher,
        );
    }

    /**
     * Test if can return false when password doesn't match.
     *
     * @return void
     */
    public function test_if_can_return_false_when_password_doesnt_match(): void
    {
        $actual = '123456789';

        $wrong = '12345678';

        $this->hasher
            ->shouldReceive('check')
            ->once()
            ->with($wrong, $actual)
            ->andReturnFalse();

        $this->assertFalse(
            $this->hashService->check($wrong, $actual),
        );
    }

    /**
     * Test if can return true when password match.
     *
     * @return void
     */
    public function test_if_can_return_true_when_password_match(): void
    {
        $actual = '123456789';

        $wrong = '123456789';

        $this->hasher
            ->shouldReceive('check')
            ->once()
            ->with($wrong, $actual)
            ->andReturnTrue();

        $this->assertTrue(
            $this->hashService->check($wrong, $actual),
        );
    }
}
