<?php

namespace Tests\Unit\Services\Authentication;

use Mockery;
use Tests\TestCase;
use App\Models\User;
use Mockery\MockInterface;
use Illuminate\Support\Facades\Event;
use App\Events\Authentication\LoginSuccessful;
use App\Services\Authentication\AuthenticationService;
use App\Contracts\Repositories\Authentication\AuthRepositoryInterface;
use App\Contracts\Services\Authentication\AuthenticationServiceInterface;
use App\Contracts\Services\{
    UserServiceInterface,
    HashServiceInterface,
};
use App\DTOs\Authentication\{
    LoginResult,
    LoginAttempt,
};
use App\Exceptions\Authentication\{
    UserIsBlockedException,
    InvalidCredentialsException,
};

class AuthenticationServiceTest extends TestCase
{
    /**
     * The user service mock.
     *
     * @var UserServiceInterface&MockInterface
     */
    private UserServiceInterface&MockInterface $userService;

    /**
     * The hash service mock.
     *
     * @var HashServiceInterface&MockInterface
     */
    private HashServiceInterface&MockInterface $hashService;

    /**
     * The auth repository mock.
     *
     * @var AuthRepositoryInterface&MockInterface
     */
    private AuthRepositoryInterface&MockInterface $authRepository;

    /**
     * The authentication service.
     *
     * @var AuthenticationServiceInterface
     */
    private AuthenticationServiceInterface $authenticationService;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->userService = Mockery::mock(UserServiceInterface::class);
        $this->hashService = Mockery::mock(HashServiceInterface::class);
        $this->authRepository = Mockery::mock(AuthRepositoryInterface::class);

        $this->authenticationService = new AuthenticationService(
            $this->userService,
            $this->hashService,
            $this->authRepository,
        );

        Event::fake();
    }

    /**
     * Test if can't authenticate if user wasn't found.
     *
     * @return void
     */
    public function test_if_cant_authenticate_if_user_wasnt_found(): void
    {
        $credentials = [
            'email' => 'notfound@gmail.com',
            'password' => 'password12345678',
        ];

        $dto = LoginAttempt::fromRequest($credentials);

        $this->userService
            ->shouldReceive('findBy')
            ->once()
            ->with('email', $dto->email)
            ->andReturnNull();

        $this->expectException(InvalidCredentialsException::class);

        $this->authenticationService->login($dto);
    }

    /**
     * Test if can't authenticate if password doesn't match.
     *
     * @return void
     */
    public function test_if_cant_authenticate_if_password_doesnt_match(): void
    {
        $credentials = [
            'email' => 'notfound@gmail.com',
            'password' => 'password12345678',
        ];

        $dto = LoginAttempt::fromRequest($credentials);

        /** @var User&MockInterface $user */
        $user = Mockery::mock(User::class);
        $user
            ->shouldReceive('getAuthPassword')
            ->twice()
            ->withNoArgs()
            ->andReturn('anotherpassword12345678');

        $this->userService
            ->shouldReceive('findBy')
            ->once()
            ->with('email', $dto->email)
            ->andReturn($user);

        $this->hashService
            ->shouldReceive('check')
            ->once()
            ->with($dto->password, $user->getAuthPassword())
            ->andReturnFalse();

        $this->expectException(InvalidCredentialsException::class);

        $this->authenticationService->login($dto);
    }

    /**
     * Test if can't authenticate if user is blocked.
     *
     * @return void
     */
    public function test_if_cant_authenticate_if_user_is_blocked(): void
    {
        $credentials = [
            'email' => 'notfound@gmail.com',
            'password' => 'password12345678',
        ];

        $dto = LoginAttempt::fromRequest($credentials);

        /** @var User&MockInterface $user */
        $user = Mockery::mock(User::class);
        $user
            ->shouldReceive('getAuthPassword')
            ->twice()
            ->withNoArgs()
            ->andReturn($credentials['password']);
        $user
            ->shouldReceive('getAttribute')
            ->with('blocked')
            ->andReturnTrue();

        $this->userService
            ->shouldReceive('findBy')
            ->once()
            ->with('email', $dto->email)
            ->andReturn($user);

        $this->hashService
            ->shouldReceive('check')
            ->once()
            ->with($dto->password, $user->getAuthPassword())
            ->andReturnTrue();

        $this->expectException(UserIsBlockedException::class);

        $this->authenticationService->login($dto);
    }

    /**
     * Test if can successful authenticate if user is ok.
     *
     * @return void
     */
    public function test_if_can_successfully_authenticate_if_user_is_ok(): void
    {
        $token = fake()->md5();

        $credentials = [
            'email' => 'notfound@gmail.com',
            'password' => 'password12345678',
        ];

        $dto = LoginAttempt::fromRequest($credentials);

        /** @var User&MockInterface $user */
        $user = Mockery::mock(User::class)->makePartial();
        $user
            ->shouldReceive('getAuthPassword')
            ->twice()
            ->withNoArgs()
            ->andReturn($credentials['password']);
        $user
            ->shouldReceive('getAttribute')
            ->with('blocked')
            ->andReturnFalse();

        $this->userService
            ->shouldReceive('findBy')
            ->once()
            ->with('email', $dto->email)
            ->andReturn($user);

        $this->hashService
            ->shouldReceive('check')
            ->once()
            ->with($dto->password, $user->getAuthPassword())
            ->andReturnTrue();

        $this->authRepository
            ->shouldReceive('attempt')
            ->once()
            ->with($dto)
            ->andReturn($token);

        $expected = new LoginResult(
            'Usuário autenticado com sucesso!',
            $token,
        );

        $result = $this->authenticationService->login($dto);
        
        Event::assertDispatched(LoginSuccessful::class, fn (LoginSuccessful $event) => $event->user === $user);

        $this->assertEquals($expected, $result);
    }
}
