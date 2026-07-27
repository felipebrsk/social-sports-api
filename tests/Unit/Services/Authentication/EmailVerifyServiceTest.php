<?php

namespace Tests\Unit\Services\Authentication;

use Mockery;
use Tests\TestCase;
use App\Models\User;
use Mockery\MockInterface;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Event;
use App\DTOs\Authentication\EmailVerify;
use App\Contracts\Services\UserServiceInterface;
use App\Services\Authentication\EmailVerifyService;
use App\Exceptions\Authentication\{
    InvalidEmailVerifyDataException,
    UserEmailAlreadyVerifiedException,
};
use App\Contracts\Services\Authentication\{
    AuthContextServiceInterface,
    EmailVerifyServiceInterface,
};

class EmailVerifyServiceTest extends TestCase
{
    /**
     * The user service mock.
     *
     * @var UserServiceInterface&MockInterface
     */
    private UserServiceInterface&MockInterface $userService;

    /**
     * The auth context service.
     *
     * @var AuthContextServiceInterface&MockInterface
     */
    private AuthContextServiceInterface&MockInterface $authContextService;

    /**
     * The email verify service.
     *
     * @var EmailVerifyServiceInterface
     */
    private EmailVerifyServiceInterface $emailVerifyService;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->userService = Mockery::mock(UserServiceInterface::class);
        $this->authContextService = Mockery::mock(AuthContextServiceInterface::class);

        $this->emailVerifyService = new EmailVerifyService(
            $this->userService,
            $this->authContextService,
        );
    }

    /**
     * Test if can't resend email if email already verified.
     *
     * @return void
     */
    public function test_if_cant_resend_email_if_email_already_verified(): void
    {
        /** @var User&MockInterface $user */
        $user = Mockery::mock(User::class);
        $user
            ->shouldReceive('hasVerifiedEmail')
            ->once()
            ->withNoArgs()
            ->andReturnTrue();

        $this->authContextService
            ->shouldReceive('user')
            ->once()
            ->withNoArgs()
            ->andReturn($user);

        $this->expectException(UserEmailAlreadyVerifiedException::class);

        $this->emailVerifyService->resend();
    }

    /**
     * Test if can resend email if email is not verified yet.
     *
     * @return void
     */
    public function test_if_cant_resend_email_if_email_is_not_verified_yet(): void
    {
        /** @var User&MockInterface $user */
        $user = Mockery::mock(User::class);
        $user
            ->shouldReceive('hasVerifiedEmail')
            ->once()
            ->withNoArgs()
            ->andReturnFalse();
        $user
            ->shouldReceive('sendEmailVerificationNotification')
            ->once()
            ->withNoArgs()
            ->andReturnNull();

        $this->authContextService
            ->shouldReceive('user')
            ->once()
            ->withNoArgs()
            ->andReturn($user);

        $this->emailVerifyService->resend();

        $this->assertEquals(3, Mockery::getContainer()->mockery_getExpectationCount());
    }

    /**
     * Test if throws exception when user ID does not match.
     */
    public function test_throws_exception_when_user_id_does_not_match(): void
    {
        $dto = new EmailVerify(id: '1', hash: 'some-hash');

        /** @var User&MockInterface $user */
        $user = Mockery::mock(User::class);
        $user
            ->shouldReceive('getAttribute')
            ->with('id')
            ->andReturn('2');

        $this->userService
            ->shouldReceive('findOrFail')
            ->once()
            ->with($dto->id)
            ->andReturn($user);

        $this->expectException(InvalidEmailVerifyDataException::class);

        $this->emailVerifyService->verify($dto);
    }

    /**
     * Test if throws exception when email hash does not match.
     */
    public function test_throws_exception_when_hash_does_not_match(): void
    {
        $dto = new EmailVerify(id: '1', hash: 'invalid-hash');

        /** @var User&MockInterface $user */
        $user = Mockery::mock(User::class);
        $user
            ->shouldReceive('getAttribute')
            ->with('id')
            ->andReturn('1');
        $user
            ->shouldReceive('getEmailForVerification')
            ->once()
            ->withNoArgs()
            ->andReturn('user@example.com');

        $this->userService
            ->shouldReceive('findOrFail')
            ->once()
            ->with($dto->id)
            ->andReturn($user);

        $this->expectException(InvalidEmailVerifyDataException::class);

        $this->emailVerifyService->verify($dto);
    }

    /**
     * Test verify does nothing if email is already verified.
     */
    public function test_verify_does_nothing_if_email_already_verified(): void
    {
        Event::fake();

        $email = 'user@example.com';
        $validHash = sha1($email);
        $dto = new EmailVerify(id: '1', hash: $validHash);

        /** @var User&MockInterface $user */
        $user = Mockery::mock(User::class);
        $user
            ->shouldReceive('getAttribute')
            ->with('id')
            ->andReturn('1');
        $user
            ->shouldReceive('getEmailForVerification')
            ->once()
            ->withNoArgs()
            ->andReturn($email);
        $user
            ->shouldReceive('hasVerifiedEmail')
            ->once()
            ->withNoArgs()
            ->andReturnTrue();

        $this->userService
            ->shouldReceive('findOrFail')
            ->once()
            ->with($dto->id)
            ->andReturn($user);

        $this->emailVerifyService->verify($dto);

        Event::assertNotDispatched(Verified::class);
    }

    /**
     * Test successful email verification dispatches event.
     */
    public function test_successful_email_verification_dispatches_event(): void
    {
        Event::fake();

        $email = 'user@example.com';
        $validHash = sha1($email);
        $dto = new EmailVerify(id: '1', hash: $validHash);

        /** @var User&MockInterface $user */
        $user = Mockery::mock(User::class);
        $user
            ->shouldReceive('getAttribute')
            ->with('id')
            ->andReturn('1');
        $user
            ->shouldReceive('getEmailForVerification')
            ->once()
            ->withNoArgs()
            ->andReturn($email);
        $user
            ->shouldReceive('hasVerifiedEmail')
            ->once()
            ->withNoArgs()
            ->andReturnFalse();
        $user
            ->shouldReceive('markEmailAsVerified')
            ->once()
            ->withNoArgs()
            ->andReturnTrue();

        $this->userService
            ->shouldReceive('findOrFail')
            ->once()
            ->with($dto->id)
            ->andReturn($user);

        $this->emailVerifyService->verify($dto);

        Event::assertDispatched(Verified::class, function ($event) use ($user) {
            return $event->user === $user;
        });
    }
}
