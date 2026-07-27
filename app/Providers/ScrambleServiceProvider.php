<?php

namespace App\Providers;

use App\Models\User;
use Dedoc\Scramble\Scramble;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\{
    Str,
    ServiceProvider,
};

class ScrambleServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Scramble::ignoreDefaultRoutes();

        Scramble::configure()->expose(
            ui: '/docs/api',
            document: '/docs/openapi.json',
        );

        Scramble::routes(function (Route $route) {
            $uri = $route->uri();
            $name = $route->getName() ?? '';

            if (Str::startsWith($uri, 'docs/')) {
                return false;
            }

            if (Str::startsWith($uri, 'storage/')) {
                return false;
            }

            if (Str::startsWith($name, 'storage.')) {
                return false;
            }

            if (Str::startsWith($name, 'horizon')) {
                return false;
            }

            return true;
        });

        Gate::define('viewApiDocs', fn (User $user) => true);
    }
}
