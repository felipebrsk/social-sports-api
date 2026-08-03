<?php

namespace App\Services\Authentication;

use App\Enums\ProviderEnum;
use App\Filters\WhereCriteria;
use Illuminate\Support\Facades\DB;
use Google\Client as GoogleClient;
use App\Contracts\Services\UserServiceInterface;
use App\Contracts\Repositories\SocialAccountRepositoryInterface;
use App\Contracts\Services\Authentication\GoogleServiceInterface;
use App\Contracts\Repositories\Authentication\AuthRepositoryInterface;
use App\Models\{
    User,
    SocialAccount,
};
use Illuminate\Support\{
    Str,
    Carbon,
};
use App\DTOs\Authentication\{
    GoogleAuthenticationResult,
    GoogleAuthenticationAttempt,
};
use App\Exceptions\Authentication\Google\{
    InvalidIdTokenException,
    UnverifiedGoogleEmailException,
};

class GoogleService implements GoogleServiceInterface
{
    /**
     * The google client.
     *
     * @var GoogleClient
     */
    private readonly GoogleClient $googleClient;

    /**
     * Create a new service instance.
     *
     * @param UserServiceInterface $userService
     * @param AuthRepositoryInterface $authRepository
     * @param SocialAccountRepositoryInterface $socialAccountRepository
     * @return void
     */
    public function __construct(
        private readonly UserServiceInterface $userService,
        private readonly AuthRepositoryInterface $authRepository,
        private readonly SocialAccountRepositoryInterface $socialAccountRepository,
    ) {
        /** @var string $clientId */
        $clientId = config('services.google.client_id');

        $this->googleClient = new GoogleClient([
            'client_id' => $clientId,
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function login(GoogleAuthenticationAttempt $data): GoogleAuthenticationResult
    {
        return DB::transaction(function () use ($data) {
            /** @var array<string, mixed>|false $payload */
            $payload = $this->googleClient->verifyIdToken($data->idToken);

            $this->assertCanLogin($payload);

            /** @var array<string, mixed> $payload */
            $user = $this->findOrCreateUser($payload);

            $token = $this->authRepository->loginByUser($user);

            $result = new GoogleAuthenticationResult(
                'Usuário autenticado com sucesso!',
                $token,
            );

            return $result;
        });
    }

    /**
     * Find or create the user through google payload.
     *
     * @param  array<string, mixed>  $payload
     * @return User
     */
    private function findOrCreateUser(array $payload): User
    {
        /** @var string $googleId */
        $googleId = $payload['sub'];

        /** @var string $email */
        $email = $payload['email'];

        /** @var string $name */
        $name = $payload['name'];

        $user = $this->userService->findBy('email', $email);

        if ($user) {
            $this->syncSocialAccounts($user, $googleId);

            return $user;
        }

        $user = $this->userService->create([
            'name' => $name,
            'email' => $email,
            'email_verified_at' => Carbon::now(),
            'password' => Str::random(40),
        ]);

        $this->syncSocialAccounts($user, $googleId);

        $user->profile()->create();

        return $user;
    }

    /**
     * Sync social accounts for user.
     *
     * @param User $user
     * @param string $googleId
     * @return void
     */
    private function syncSocialAccounts(User $user, string $googleId): void
    {
        /** @var WhereCriteria<SocialAccount> $userCriteria */
        $userCriteria = new WhereCriteria('user_id', $user->id);

        /** @var WhereCriteria<SocialAccount> $providerCriteria */
        $providerCriteria = new WhereCriteria('provider_id', ProviderEnum::GOOGLE_ID->value);

        $exists = $this->socialAccountRepository->withCriteria(
            $userCriteria,
            $providerCriteria,
        )->exists();

        if (! $exists) {
            $this->socialAccountRepository->create([
                'user_id' => $user->id,
                'identifier' => $googleId,
                'provider_id' => ProviderEnum::GOOGLE_ID->value,
            ]);
        }
    }

    /**
     * Assert can login through google provider.
     *
     * @param array<string, mixed>|false $payload
     * @throws InvalidIdTokenException
     * @throws UnverifiedGoogleEmailException
     * @return void
     */
    private function assertCanLogin(array|false $payload): void
    {
        if (! $payload) {
            throw new InvalidIdTokenException();
        }

        if (! ($payload['email_verified'] ?? false)) {
            throw new UnverifiedGoogleEmailException();
        }
    }
}
