<?php

namespace Tests\Unit\Repositories\Authentication;

use Mockery;
use Tests\TestCase;
use App\Models\User;
use Mockery\MockInterface;
use Tymon\JWTAuth\JWTGuard;
use App\DTOs\Authentication\LoginAttempt;
use App\Repositories\Authentication\AuthRepository;
use Illuminate\Contracts\Auth\Factory as AuthFactory;
use App\Contracts\Repositories\Authentication\AuthRepositoryInterface;

class AuthRepositoryTest extends TestCase
{
    /**
     * The authentication guard instance.
     *
     * @var JWTGuard&MockInterface
     */
    private JWTGuard&MockInterface $guard;

    /**
     * The authentication factory instance.
     *
     * @var AuthFactory&MockInterface
     */
    private AuthFactory&MockInterface $factory;

    /**
     * The auth context repository.
     *
     * @var AuthRepositoryInterface
     */
    private AuthRepositoryInterface $authRepository;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->guard = Mockery::mock(JWTGuard::class);
        $this->factory = Mockery::mock(AuthFactory::class);

        $this->factory
            ->shouldReceive('guard')
            ->with('api')
            ->andReturn($this->guard);

        $this->authRepository = new AuthRepository(
            $this->factory,
        );
    }

    /**
     * Test if can authenticate by credentials correctly.
     *
     * @return void
     */
    public function test_if_can_correctly_authenticate_by_credentials(): void
    {
        $token = fake()->md5();

        $credentials = [
            'email' => fake()->email(),
            'password' => fake()->word(),
        ];

        $dto = LoginAttempt::fromRequest($credentials);

        $this->guard
            ->shouldReceive('attempt')
            ->once()
            ->with($credentials)
            ->andReturn($token);

        $result = $this->authRepository->attempt($dto);

        $this->assertSame($token, $result);
    }

    /**
     * Test if can authenticate by a given user instance.
     *
     * @return void
     */
    public function test_if_can_authenticate_by_a_given_user_instance(): void
    {
        $token = fake()->md5();

        /** @var User&MockInterface $user */
        $user = Mockery::mock(User::class);

        $this->guard
            ->shouldReceive('login')
            ->once()
            ->with($user)
            ->andReturn($token);

        $result = $this->authRepository->loginByUser($user);

        $this->assertSame($token, $result);
    }
}
