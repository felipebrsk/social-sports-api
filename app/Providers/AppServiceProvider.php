<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Validation\Rules\Password;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Auth\Passwords\{
    DatabaseTokenRepository,
    TokenRepositoryInterface,
};
use Illuminate\Support\Facades\{
    DB,
    URL,
};

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->environment(['homolog', 'production'])) {
            URL::forceScheme('https');
        }

        $this->bindTokenRepository();
        $this->configureModelStrict();
        $this->configurePasswordPattern();
        $this->configurePasswordResetUrl();
        $this->prohibitDestructiveCommands();
    }

    /**
     * Configure the password patterns.
     *
     * @return void
     */
    private function configurePasswordPattern(): void
    {
        Password::defaults(function () {
            return Password::min(8)
                ->max(16)
                ->rules(['string', 'confirmed'])
                ->letters()
                ->symbols()
                ->mixedCase()
                ->numbers()
                ->uncompromised();
        });
    }

    /**
     * Configure models to be strict.
     *
     * @return void
     */
    private function configureModelStrict(): void
    {
        Model::shouldBeStrict(! $this->app->environment(['production']));
    }

    /**
     * Prohibit destructive commands on database.
     *
     * @return void
     */
    private function prohibitDestructiveCommands(): void
    {
        DB::prohibitDestructiveCommands($this->app->environment(['production']));
    }

    /**
     * Bind the custom token repository.
     *
     * @return void
     */
    private function bindTokenRepository(): void
    {
        $this->app->bind(TokenRepositoryInterface::class, function (Application $app) {
            /** @var string $appKey */
            $appKey = config('app.key');

            return new DatabaseTokenRepository(
                DB::connection(),
                $app->make(Hasher::class),
                'password_reset_tokens',
                $appKey,
            );
        });
    }

    /**
     * Configure the url for the password reset.
     *
     * @return void
     */
    private function configurePasswordResetUrl(): void
    {
        ResetPassword::createUrlUsing(function (mixed $notifiable, string $token) {
            /** @var string $baseUrl */
            $baseUrl = config('app.frontend.base_url');

            $baseUrl = rtrim($baseUrl, '/');

            /** @var User $notifiable */
            return "{$baseUrl}/password/reset/{$token}/?email={$notifiable->getEmailForPasswordReset()}";
        });
    }
}
