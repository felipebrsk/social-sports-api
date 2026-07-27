<?php

namespace Tests\Unit\Services\Authentication;

use Mockery;
use Tests\TestCase;
use App\Models\User;
use Mockery\MockInterface;
use Illuminate\Auth\AuthenticationException;
use App\Services\Authentication\AuthContextService;
use App\Contracts\Repositories\Authentication\AuthContextRepositoryInterface;

class AuthContextServiceTest extends TestCase
{
    /**
     * The auth context repository mock.
     */
    private AuthContextRepositoryInterface&MockInterface $authContextRepositoryMock;

    /**
     * The auth context service instance under test.
     */
    private AuthContextService $authContextService;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->authContextRepositoryMock = Mockery::mock(AuthContextRepositoryInterface::class);

        $this->authContextService = new AuthContextService(
            $this->authContextRepositoryMock,
        );
    }

    /**
     * Test if id returns user id when authenticated.
     *
     * @return void
     */
    public function test_if_id_returns_user_id_when_authenticated(): void
    {
        $id = 182931;

        $this->authContextRepositoryMock
            ->shouldReceive('check')
            ->once()
            ->andReturnTrue();

        $this->authContextRepositoryMock
            ->shouldReceive('id')
            ->once()
            ->andReturn($id);

        $result = $this->authContextService->id();

        $this->assertSame($id, $result);
    }

    /**
     * Test if id throws authentication exception when unauthenticated.
     *
     * @return void
     */
    public function test_if_id_throws_exception_when_unauthenticated(): void
    {
        $this->authContextRepositoryMock
            ->shouldReceive('check')
            ->once()
            ->andReturnFalse();

        $this->authContextRepositoryMock->shouldNotReceive('id');

        $this->expectException(AuthenticationException::class);
        $this->expectExceptionMessage('Usuário não autenticado.');

        $this->authContextService->id();
    }

    /**
     * Test if user returns user instance when authenticated.
     *
     * @return void
     */
    public function test_if_user_returns_user_model_when_authenticated(): void
    {
        $id = 1829312;

        /** @var MockInterface&User $user */
        $user = Mockery::mock(User::class);
        $user
            ->shouldReceive('getAttribute')
            ->with('id')
            ->andReturn($id);

        $this->authContextRepositoryMock
            ->shouldReceive('check')
            ->once()
            ->andReturnTrue();

        $this->authContextRepositoryMock
            ->shouldReceive('user')
            ->once()
            ->andReturn($user);

        $result = $this->authContextService->user();

        $this->assertSame($user, $result);
        $this->assertSame($id, $result->id);
    }

    /**
     * Test if user throws authentication exception when unauthenticated.
     *
     * @return void
     */
    public function test_if_user_throws_exception_when_unauthenticated(): void
    {
        $this->authContextRepositoryMock
            ->shouldReceive('check')
            ->once()
            ->andReturnFalse();

        $this->authContextRepositoryMock->shouldNotReceive('user');

        $this->expectException(AuthenticationException::class);
        $this->expectExceptionMessage('Usuário não autenticado.');

        $this->authContextService->user();
    }

    /**
     * Test if check returns true when user is authenticated.
     *
     * @return void
     */
    public function test_if_check_returns_true_when_authenticated(): void
    {
        $this->authContextRepositoryMock
            ->shouldReceive('check')
            ->once()
            ->andReturnTrue();

        $this->assertTrue($this->authContextService->check());
    }

    /**
     * Test if check returns false when user is not authenticated.
     *
     * @return void
     */
    public function test_if_check_returns_false_when_unauthenticated(): void
    {
        $this->authContextRepositoryMock
            ->shouldReceive('check')
            ->once()
            ->andReturnFalse();

        $this->assertFalse($this->authContextService->check());
    }

    /**
     * Test if assertAuthenticated runs cleanly when user is logged in.
     *
     * @return void
     */
    public function test_if_assert_authenticated_passes_when_authenticated(): void
    {
        $this->authContextRepositoryMock
            ->shouldReceive('check')
            ->once()
            ->andReturnTrue();

        $this->authContextService->assertAuthenticated();

        $this->assertEquals(1, Mockery::getContainer()->mockery_getExpectationCount());
    }
}
