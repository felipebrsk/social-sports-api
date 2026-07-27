<?php

namespace Tests\Unit\Services\Authentication;

use Mockery;
use Tests\TestCase;
use App\Models\User;
use Mockery\MockInterface;
use App\Contracts\Services\UserServiceInterface;
use App\Services\Authentication\RegisterService;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Contracts\Services\Authentication\RegisterServiceInterface;
use App\Contracts\Repositories\Authentication\AuthRepositoryInterface;
use App\DTOs\Authentication\{
    RegisterUser,
    RegisterResult,
};

class RegisterServiceTest extends TestCase
{
    /**
     * The user service mock.
     *
     * @var UserServiceInterface&MockInterface
     */
    private UserServiceInterface&MockInterface $userService;

    /**
     * The auth repository mock.
     *
     * @var AuthRepositoryInterface&MockInterface
     */
    private AuthRepositoryInterface&MockInterface $authRepository;

    /**
     * The register service.
     *
     * @var RegisterServiceInterface
     */
    private RegisterServiceInterface $registerService;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->userService = Mockery::mock(UserServiceInterface::class);
        $this->authRepository = Mockery::mock(AuthRepositoryInterface::class);

        $this->registerService = new RegisterService(
            $this->userService,
            $this->authRepository,
        );
    }

    /**
     * Test if can correctly register the given user.
     *
     * @return void
     */
    public function test_if_can_correctly_register_the_given_user(): void
    {
        $token = fake()->md5();

        $data = [
            'name' => fake()->name(),
            'email' => fake()->email(),
            'password' => fake()->password(),
        ];

        $dto = RegisterUser::fromRequest($data);

        /** @var User&MockInterface $user */
        $user = Mockery::mock(User::class);

        $profileRelation = Mockery::mock(HasOne::class);
        $profileRelation
            ->shouldReceive('create')
            ->once()
            ->withNoArgs();

        $user
            ->shouldReceive('profile')
            ->once()
            ->withNoArgs()
            ->andReturn($profileRelation);
        $user
            ->shouldReceive('sendEmailVerificationNotification')
            ->once()
            ->withNoArgs();

        $this->userService
            ->shouldReceive('create')
            ->once()
            ->with([
                'name' => $dto->name,
                'email' => $dto->email,
                'password' => $dto->password,
            ])->andReturn($user);

        $this->authRepository
            ->shouldReceive('loginByUser')
            ->once()
            ->with($user)
            ->andReturn($token);

        $result = $this->registerService->register($dto);

        $this->assertInstanceOf(RegisterResult::class, $result);
    }
}
