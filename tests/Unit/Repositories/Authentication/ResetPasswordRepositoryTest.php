<?php

namespace Tests\Unit\Repositories\Authentication;

use Mockery;
use Tests\TestCase;
use App\Models\User;
use Mockery\MockInterface;
use Illuminate\Auth\Passwords\TokenRepositoryInterface;
use App\Repositories\Authentication\ResetPasswordRepository;
use App\Contracts\Repositories\Authentication\ResetPasswordRepositoryInterface;

class ResetPasswordRepositoryTest extends TestCase
{
    /**
     * The token repository.
     *
     * @var TokenRepositoryInterface&MockInterface
     */
    private TokenRepositoryInterface&MockInterface $tokenRepository;

    /**
     * The reset password repository.
     *
     * @var ResetPasswordRepositoryInterface
     */
    private ResetPasswordRepositoryInterface $resetPasswordRepository;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->tokenRepository = Mockery::mock(TokenRepositoryInterface::class);

        $this->resetPasswordRepository = new ResetPasswordRepository(
            $this->tokenRepository,
        );
    }

    /**
     * Test if can create a reset token successfully.
     *
     * @return void
     */
    public function test_if_can_correctly_create_reset_token(): void
    {
        $expectedToken = fake()->sha256();

        $user = Mockery::mock(User::class);

        $this->tokenRepository
            ->shouldReceive('create')
            ->once()
            ->with($user)
            ->andReturn($expectedToken);

        $actualToken = $this->resetPasswordRepository->createResetToken($user);

        $this->assertSame($expectedToken, $actualToken);
    }

    /**
     * Test if can correctly check if user recently created a token.
     *
     * @return void
     */
    public function test_if_can_correctly_check_if_recently_created_token(): void
    {
        $user = Mockery::mock(User::class);

        $this->tokenRepository
            ->shouldReceive('recentlyCreatedToken')
            ->once()
            ->with($user)
            ->andReturnTrue();

        $actual = $this->resetPasswordRepository->recentlyCreatedToken($user);

        $this->assertTrue($actual);
    }

    /**
     * Test if can correctly check if a specific token exists for the user.
     *
     * @return void
     */
    public function test_if_can_correctly_check_if_token_exists(): void
    {
        $token = fake()->sha256();

        $user = Mockery::mock(User::class);

        $this->tokenRepository
            ->shouldReceive('exists')
            ->once()
            ->with($user, $token)
            ->andReturnTrue();

        $actual = $this->resetPasswordRepository->exists($user, $token);

        $this->assertTrue($actual);
    }

    /**
     * Test if can correctly delete a token for given user.
     *
     * @return void
     */
    public function test_if_can_correctly_delete_token(): void
    {
        $user = Mockery::mock(User::class);

        $this->tokenRepository
            ->shouldReceive('delete')
            ->once()
            ->with($user)
            ->andReturnNull();

        $this->resetPasswordRepository->delete($user);

        $this->assertEquals(1, Mockery::getContainer()->mockery_getExpectationCount());
    }
}
