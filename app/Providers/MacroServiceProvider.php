<?php

namespace App\Providers;

use Throwable;
use ReflectionClass;
use ReflectionMethod;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Eloquent\{
    Model,
    Builder,
};

use function get_class;

class MacroServiceProvider extends ServiceProvider
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
        $this->configureModelMacroForRelations();
    }

    /**
     * Create a macro for a new attribute on models to get relations.
     */
    private function configureModelMacroForRelations(): void
    {
        Builder::macro('getModelRelationships', function () {
            /** @var Builder<Model> $builder */
            $builder = $this;

            $model = $builder->getModel();

            /** @var array<string, class-string> $relationships */
            $relationships = [];
            $reflection = new ReflectionClass($model);

            foreach ($reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
                if ($method->class !== get_class($model)) {
                    continue;
                }

                if ($method->getNumberOfParameters() > 0) {
                    continue;
                }

                try {
                    $result = $method->invoke($model);
                    if ($result instanceof Relation) {
                        $relationships[$method->name] = get_class($result);
                    }
                } catch (Throwable) {
                    continue;
                }
            }

            return $relationships;
        });
    }
}
