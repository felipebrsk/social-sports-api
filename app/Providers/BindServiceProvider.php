<?php

namespace App\Providers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\{
    Str,
    ServiceProvider,
};

class BindServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->registerAutoBindings();

        $this->bindCustoms();
    }

    /**
     * Discover and bind automatically interfaces to concrete instances.
     *
     * @return void
     */
    private function registerAutoBindings(): void
    {
        $contractsPath = app_path('Contracts');

        if (! File::isDirectory($contractsPath)) {
            return;
        }

        $files = File::allFiles($contractsPath);

        foreach ($files as $file) {
            $interfaceClass = 'App\\Contracts\\' . str_replace('/', '\\', $file->getRelativePathname());
            $interfaceClass = Str::replaceLast('.php', '', $interfaceClass);

            $concreteClass = Str::replaceFirst('Contracts\\', '', $interfaceClass);
            $concreteClass = Str::replaceLast('Interface', '', $concreteClass);

            if (class_exists($concreteClass) && interface_exists($interfaceClass)) {
                $this->app->scoped($interfaceClass, $concreteClass);
            }
        }
    }

    /**
     * Bind customs.
     *
     * @return void
     */
    public function bindCustoms(): void
    {
        //
    }
}
