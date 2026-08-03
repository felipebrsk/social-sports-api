<?php

namespace Tests\Feature\Http\Authentication;

use Mockery;
use ReflectionClass;
use App\Models\User;
use Mockery\MockInterface;
use App\Enums\ProviderEnum;
use Google\Client as GoogleClient;
use Database\Seeders\ProviderSeeder;
use Tests\Feature\BaseIntegrationTesting;
use App\Services\Authentication\GoogleService;

class GoogleLoginTest extends BaseIntegrationTesting
{
    /**
     * Get the route name.
     *
     * @return string
     */
    private function getRouteName(): string
    {
        return 'authentication.oauth.google';
    }

    /**
     * Setup new test environments.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            ProviderSeeder::class,
        ]);
    }

    /**
     * Test if can't login without id_token.
     *
     * @return void
     */
    public function test_if_cant_login_without_id_token(): void
    {
        $this->postJson(route($this->getRouteName()), [])
            ->assertUnprocessable()
            ->assertInvalid(['id_token'])
            ->assertSee('O campo token \u00e9 obrigat\u00f3rio.');
    }

    /**
     * Test if can throw unprocessable if id token is invalid.
     *
     * @return void
     */
    public function test_if_can_throw_unprocessable_if_id_token_is_invalid(): void
    {
        $googleClient = $this->mockGoogleClient();
        $googleClient
            ->shouldReceive('verifyIdToken')
            ->once()
            ->with('invalid-token')
            ->andReturnFalse();

        $this->postJson(route($this->getRouteName()), [
            'id_token' => 'invalid-token',
        ])->assertUnprocessable()->assertSee('Token do Google inv\u00e1lido ou expirado.');
    }

    /**
     * Test if can throw unprocessable if google email is unverified.
     *
     * @return void
     */
    public function test_if_can_throw_unprocessable_if_google_email_is_unverified(): void
    {
        $googleClient = $this->mockGoogleClient();
        $googleClient
            ->shouldReceive('verifyIdToken')
            ->once()
            ->with('unverified-token')
            ->andReturn([
                'sub' => 'google-123',
                'email_verified' => false,
                'name' => 'Unverified User',
                'email' => 'unverified@example.com',
            ]);

        $this->postJson(route($this->getRouteName()), [
            'id_token' => 'unverified-token',
        ])->assertUnprocessable()->assertSee('O e-mail da conta Google n\u00e3o est\u00e1 verificado. Tente verificar o e-mail pelo Google ou acesse com o seu e-mail normalmente.');
    }

    /**
     * Test if can authenticate existing user with google.
     *
     * @return void
     */
    public function test_if_can_authenticate_existing_user_with_google(): void
    {
        $this->assertDatabaseEmpty('social_accounts');

        $googleClient = $this->mockGoogleClient();
        $googleClient
            ->shouldReceive('verifyIdToken')
            ->once()
            ->with('valid-token')
            ->andReturn([
                'email_verified' => true,
                'name' => $this->user->name,
                'email' => $this->user->email,
                'sub' => $identifier = 'google-123',
            ]);

        $this->postJson(route($this->getRouteName()), [
            'id_token' => 'valid-token',
        ])->assertOk();

        $this->assertDatabaseHas('social_accounts', [
            'identifier' => $identifier,
            'user_id' => $this->user->id,
            'provider_id' => ProviderEnum::GOOGLE_ID->value,
        ]);
    }

    /**
     * Test if can create and authenticate new user with google.
     *
     * @return void
     */
    public function test_if_can_create_and_authenticate_new_user_with_google(): void
    {
        $email = 'newuser@example.com';

        $this->assertDatabaseMissing('users', [
            'email' => $email,
        ]);

        $googleClient = $this->mockGoogleClient();
        $googleClient
            ->shouldReceive('verifyIdToken')
            ->once()
            ->with('valid-token-new-user')
            ->andReturn([
                'email' => $email,
                'email_verified' => true,
                'name' => 'New Google User',
                'sub' => $identifier = 'google-456',
            ]);

        $this->postJson(route($this->getRouteName()), [
            'id_token' => 'valid-token-new-user',
        ])->assertOk();

        $this->assertDatabaseHas('users', [
            'email' => $email,
            'name' => 'New Google User',
        ]);

        $user = User::where('email', $email)->firstOrFail();

        $this->assertDatabaseHas('social_accounts', [
            'user_id' => $user->id,
            'identifier' => $identifier,
            'provider_id' => ProviderEnum::GOOGLE_ID->value,
        ]);
    }

    /**
     * Test if can get correct json structure.
     *
     * @return void
     */
    public function test_if_can_get_correct_json_structure(): void
    {
        $googleClient = $this->mockGoogleClient();
        $googleClient
            ->shouldReceive('verifyIdToken')
            ->once()
            ->with('valid-token')
            ->andReturn([
                'sub' => 'google-123',
                'email_verified' => true,
                'name' => $this->user->name,
                'email' => $this->user->email,
            ]);

        $this->postJson(route($this->getRouteName()), [
            'id_token' => 'valid-token',
        ])->assertOk()->assertJsonStructure([
            'data' => [
                'message',
                'token',
            ],
        ]);
    }

    /**
     * Test if can get correct json data.
     *
     * @return void
     */
    public function test_if_can_get_correct_json_data(): void
    {
        $googleClient = $this->mockGoogleClient();
        $googleClient
            ->shouldReceive('verifyIdToken')
            ->once()
            ->with('valid-token')
            ->andReturn([
                'sub' => 'google-123',
                'email_verified' => true,
                'name' => $this->user->name,
                'email' => $this->user->email,
            ]);

        $this->postJson(route($this->getRouteName()), [
            'id_token' => 'valid-token',
        ])->assertOk()->assertJson([
            'data' => [
                'message' => 'Usuário autenticado com sucesso!',
            ],
        ]);
    }

    /**
     * Test if can access an auth route after login.
     *
     * @return void
     */
    public function test_if_can_access_an_auth_route_after_login(): void
    {
        $this->actingAsGuest();

        $this->getJson(route('user.me'))->assertUnauthorized();

        $googleClient = $this->mockGoogleClient();
        $googleClient
            ->shouldReceive('verifyIdToken')
            ->once()
            ->with('valid-token')
            ->andReturn([
                'sub' => 'google-123',
                'email_verified' => true,
                'name' => $this->user->name,
                'email' => $this->user->email,
            ]);

        $response = $this->postJson(route($this->getRouteName()), [
            'id_token' => 'valid-token',
        ])->assertOk();

        /** @var string $token */
        $token = $response->json('data.token');

        $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson(route('user.me'))
            ->assertOk();
    }

    /**
     * Helper to bind mock GoogleClient into GoogleService instance in IOC container.
     *
     * @return GoogleClient&MockInterface
     */
    private function mockGoogleClient(): GoogleClient&MockInterface
    {
        $googleClientMock = Mockery::mock(GoogleClient::class);

        $this->app->extend(GoogleService::class, function (GoogleService $service) use ($googleClientMock) {
            $reflection = new ReflectionClass(GoogleService::class);

            $newService = $reflection->newInstanceWithoutConstructor();

            $reflection->getProperty('userService')->setValue($newService, $this->getProtectedProperty($service, 'userService'));
            $reflection->getProperty('authRepository')->setValue($newService, $this->getProtectedProperty($service, 'authRepository'));
            $reflection->getProperty('socialAccountRepository')->setValue($newService, $this->getProtectedProperty($service, 'socialAccountRepository'));
            $reflection->getProperty('googleClient')->setValue($newService, $googleClientMock);

            return $newService;
        });

        return $googleClientMock;
    }
}
