<?php

namespace Tests\Unit\Repositories\Authentication;

use Mockery;
use Tests\TestCase;
use App\Models\User;
use Mockery\MockInterface;
use Illuminate\Contracts\Auth\Guard;
use App\Repositories\Authentication\AuthContextRepository;
use App\Contracts\Repositories\Authentication\AuthContextRepositoryInterface;

class AuthContextRepositoryTest extends TestCase
{
    /**
     * The authentication guard instance.
     *
     * @var Guard&MockInterface
     */
    private Guard&MockInterface $guard;

    /**
     * The auth context repository.
     *
     * @var AuthContextRepositoryInterface
     */
    private AuthContextRepositoryInterface $authContextRepository;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->guard = Mockery::mock(Guard::class);

        $this->authContextRepository = new AuthContextRepository(
            $this->guard,
        );
    }

    /**
     * Test if can check if user is authenticated.
     *
     * @return void
     */
    public function test_if_can_check_if_user_is_authenticated(): void
    {
        $expected = fake()->boolean();

        $this->guard
            ->shouldReceive('check')
            ->once()
            ->withNoArgs()
            ->andReturn($expected);

        $actual = $this->authContextRepository->check();

        $this->assertSame($expected, $actual);
    }

    /**
     * Test if can return null if user is not authenticated on user method.
     *
     * @return void
     */
    public function test_if_can_return_null_if_user_is_not_authenticated_on_user_method(): void
    {
        $this->guard
            ->shouldReceive('user')
            ->once()
            ->withNoArgs()
            ->andReturnNull();

        $actual = $this->authContextRepository->user();

        $this->assertNull($actual);
    }

    /**
     * Test if can return user instance if user is authenticated on user method.
     *
     * @return void
     */
    public function test_if_can_return_user_instance_if_user_is_authenticated_on_user_method(): void
    {
        $user = Mockery::mock(User::class);

        $this->guard
            ->shouldReceive('user')
            ->once()
            ->withNoArgs()
            ->andReturn($user);

        $actual = $this->authContextRepository->user();

        $this->assertSame($user, $actual);
    }

    /**
     * Test if can return null if user is not authenticated on id method.
     *
     * @return void
     */
    public function test_if_can_return_null_if_user_is_not_authenticated_on_id_method(): void
    {
        $this->guard
            ->shouldReceive('id')
            ->once()
            ->withNoArgs()
            ->andReturnNull();

        $actual = $this->authContextRepository->id();

        $this->assertNull($actual);
    }

    /**
     * Test if can return user id if user is authenticated on id method.
     *
     * @return void
     */
    public function test_if_can_return_user_id_if_user_is_authenticated_on_id_method(): void
    {
        $id = 91823912;

        $this->guard
            ->shouldReceive('id')
            ->once()
            ->withNoArgs()
            ->andReturn($id);

        $actual = $this->authContextRepository->id();

        $this->assertSame($id, $actual);
    }
}
