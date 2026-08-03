<?php

namespace Tests\Unit\Services\Authentication;

use Tests\TestCase;
use Mockery;
use App\Models\User;
use App\Enums\ProviderEnum;
use Google\Client as GoogleClient;
use Illuminate\Support\Facades\Config;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Services\Authentication\GoogleService;
use App\Contracts\Services\UserServiceInterface;
use App\Contracts\Repositories\SocialAccountRepositoryInterface;
use App\Contracts\Repositories\Authentication\AuthRepositoryInterface;
use App\DTOs\Authentication\GoogleAuthenticationAttempt;
use App\DTOs\Authentication\GoogleAuthenticationResult;
use App\Exceptions\Authentication\Google\InvalidIdTokenException;
use App\Exceptions\Authentication\Google\UnverifiedGoogleEmailException;
use Mockery\MockInterface;
use ReflectionClass;

class GoogleServiceTest extends TestCase
{
    /**
     * The google client mock.
     *
     * @var GoogleClient&MockInterface
     */
    private GoogleClient&MockInterface $googleClient;

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
     * The social account repository mock.
     *
     * @var SocialAccountRepositoryInterface&MockInterface
     */
    private SocialAccountRepositoryInterface&MockInterface $socialAccountRepository;

    /**
     * The google service.
     *
     * @var GoogleService
     */
    private GoogleService $googleService;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        Config::set('services.google.client_id', 'google-client-id-123');

        $this->googleClient = Mockery::mock(GoogleClient::class);
        $this->userService = Mockery::mock(UserServiceInterface::class);
        $this->authRepository = Mockery::mock(AuthRepositoryInterface::class);
        $this->socialAccountRepository = Mockery::mock(SocialAccountRepositoryInterface::class);

        $reflection = new ReflectionClass(GoogleService::class);
        $this->googleService = $reflection->newInstanceWithoutConstructor();

        $reflection->getProperty('userService')->setValue($this->googleService, $this->userService);
        $reflection->getProperty('authRepository')->setValue($this->googleService, $this->authRepository);
        $reflection->getProperty('socialAccountRepository')->setValue($this->googleService, $this->socialAccountRepository);
        $reflection->getProperty('googleClient')->setValue($this->googleService, $this->googleClient);
    }

    /**
     * Test it should authenticate existing user successfully.
     *
     * @return void
     */
    public function test_should_authenticate_existing_user_successfully(): void
    {
        $idToken = 'valid-google-id-token';

        $attempt = new GoogleAuthenticationAttempt($idToken);

        $payload = [
            'name' => 'John Doe',
            'email_verified' => true,
            'sub' => 'google-user-id-123',
            'email' => 'john.doe@example.com',
        ];

        $user = Mockery::mock(User::class);
        $user
            ->shouldReceive('getAttribute')
            ->with('id')
            ->andReturn(1);

        $this->googleClient
            ->shouldReceive('verifyIdToken')
            ->once()
            ->with($idToken)
            ->andReturn($payload);

        $this->userService
            ->shouldReceive('findBy')
            ->once()
            ->with('email', 'john.doe@example.com')
            ->andReturn($user);

        $this->socialAccountRepository
            ->shouldReceive('withCriteria')
            ->once()
            ->andReturnSelf();
        $this->socialAccountRepository
            ->shouldReceive('exists')
            ->once()
            ->andReturnTrue();

        $this->authRepository
            ->shouldReceive('loginByUser')
            ->once()
            ->with($user)
            ->andReturn('generated-jwt-token');

        $result = $this->googleService->login($attempt);

        $this->assertInstanceOf(GoogleAuthenticationResult::class, $result);
        $this->assertEquals('Usuário autenticado com sucesso!', $result->message);
        $this->assertEquals('generated-jwt-token', $result->token);
    }

    /**
     * Test it should create and authenticate new user with social account.
     *
     * @return void
     */
    public function test_should_create_and_authenticate_new_user_with_social_account(): void
    {
        $idToken = 'valid-google-id-token';

        $attempt = new GoogleAuthenticationAttempt($idToken);

        $payload = [
            'sub' => 'google-user-id-123',
            'email' => 'jane.doe@example.com',
            'name' => 'Jane Doe',
            'email_verified' => true,
        ];

        $user = Mockery::mock(User::class);
        $user
            ->shouldReceive('getAttribute')
            ->with('id')
            ->andReturn(2);

        $hasOneRelation = Mockery::mock(HasOne::class);
        $hasOneRelation
            ->shouldReceive('create')
            ->once()
            ->andReturnSelf();

        $user
            ->shouldReceive('profile')
            ->once()
            ->andReturn($hasOneRelation);

        $this->googleClient
            ->shouldReceive('verifyIdToken')
            ->once()
            ->with($idToken)
            ->andReturn($payload);

        $this->userService
            ->shouldReceive('findBy')
            ->once()
            ->with('email', 'jane.doe@example.com')
            ->andReturnNull();

        $this->userService
            ->shouldReceive('create')
            ->once()
            ->with(Mockery::on(function (array $data) {
                return $data['name'] === 'Jane Doe'
                    && $data['email'] === 'jane.doe@example.com'
                    && isset($data['email_verified_at'])
                    && isset($data['password']);
            }))->andReturn($user);

        $this->socialAccountRepository
            ->shouldReceive('withCriteria')
            ->once()
            ->andReturnSelf();
        $this->socialAccountRepository
            ->shouldReceive('exists')
            ->once()
            ->andReturnFalse();
        $this->socialAccountRepository
            ->shouldReceive('create')
            ->once()
            ->with([
                'user_id' => 2,
                'identifier' => 'google-user-id-123',
                'provider_id' => ProviderEnum::GOOGLE_ID->value,
            ]);

        $this->authRepository
            ->shouldReceive('loginByUser')
            ->once()
            ->with($user)
            ->andReturn('generated-jwt-token-new-user');

        $result = $this->googleService->login($attempt);

        $this->assertInstanceOf(GoogleAuthenticationResult::class, $result);
        $this->assertEquals('Usuário autenticado com sucesso!', $result->message);
        $this->assertEquals('generated-jwt-token-new-user', $result->token);
    }

    /**
     * Test it should throw exception when id token is invalid.
     *
     * @return void
     */
    public function test_should_throw_exception_when_id_token_is_invalid(): void
    {
        $this->expectException(InvalidIdTokenException::class);

        $attempt = new GoogleAuthenticationAttempt('invalid-token');

        $this->googleClient
            ->shouldReceive('verifyIdToken')
            ->once()
            ->with('invalid-token')
            ->andReturnFalse();

        $this->googleService->login($attempt);
    }

    /**
     * Test it should throw exception when google email is unverified.
     *
     * @return void
     */
    public function test_should_throw_exception_when_google_email_is_unverified(): void
    {
        $this->expectException(UnverifiedGoogleEmailException::class);

        $attempt = new GoogleAuthenticationAttempt('unverified-email-token');

        $payload = [
            'sub' => 'google-user-id-123',
            'email' => 'unverified@example.com',
            'name' => 'Unverified User',
            'email_verified' => false,
        ];

        $this->googleClient
            ->shouldReceive('verifyIdToken')
            ->once()
            ->with('unverified-email-token')
            ->andReturn($payload);

        $this->googleService->login($attempt);
    }
}
