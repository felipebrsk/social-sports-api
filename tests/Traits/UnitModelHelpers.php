<?php

namespace Tests\Traits;

use Illuminate\Database\Eloquent\Model;
use ReflectionClass;

trait UnitModelHelpers
{
    /**
     * Test if the fillable attributes are correctly.
     *
     * @param  array<int, string>  $fillable
     */
    public function assertHasFillables(array $fillable): void
    {
        /** @var string $model */
        $model = $this->model();

        /** @var Model $model */
        $model = new $model();

        $this->assertEqualsCanonicalizing($fillable, $model->getFillable());
    }

    /**
     * Test if the model uses the correctly traits.
     *
     * @param  array<int, class-string>  $traits
     */
    public function assertUsesTraits(array $traits): void
    {
        /** @var string $model */
        $model = $this->model();

        /** @var Model $model */
        $model = new $model();

        $modelTraits = array_keys(class_uses($model));

        $this->assertEqualsCanonicalizing($traits, $modelTraits);
    }

    /**
     * Test if the casts attributes are correctly.
     *
     * @param  array<string, string>  $casts
     */
    public function assertHasCasts(array $casts): void
    {
        /** @var string $model */
        $model = $this->model();

        /** @var Model $model */
        $model = new $model();

        $this->assertEqualsCanonicalizing($casts, $model->getCasts());
    }

    /**
     * Test if the interfaces attributes are correct.
     *
     * @param  array<int, class-string>  $interfaces
     */
    public function assertUsesInterfaces(array $interfaces): void
    {
        /** @var string $model */
        $model = $this->model();

        /** @var Model $model */
        $model = new $model();

        foreach ($interfaces as $interface) {
            $this->assertInstanceOf($interface, $model);
        }
    }

    /**
     * Test if the relations attributes are correct.
     *
     * @param  array<string, class-string>  $relations
     */
    public function assertHasRelations(array $relations): void
    {
        /** @var string $model */
        $model = $this->model();

        /** @var Model $model */
        $model = new $model();

        $this->assertEqualsCanonicalizing($relations, $model->getModelRelationships());
    }

    /**
     * Test if the constants are correctly defined.
     *
     * @param  array<string, mixed>  $expectedConstants
     */
    public function assertHasConstants(array $expectedConstants): void
    {
        /** @var class-string $model */
        $model = $this->model();

        $reflection = new ReflectionClass($model);

        $constants = $reflection->getConstants();

        $this->assertEqualsCanonicalizing($expectedConstants, $constants);
    }

    /**
     * Test if the guarded attributes are stored correctly.
     *
     * @param  array<int, string>  $expectedGuarded
     */
    public function assertHasGuarded(array $expectedGuarded): void
    {
        /** @var string $model */
        $model = $this->model();

        /** @var Model $model */
        $model = new $model();

        $this->assertEqualsCanonicalizing($expectedGuarded, $model->getGuarded());
    }

    /**
     * Test if table attribute is correctly set.
     */
    public function assertHasTable(string $expectedTable): void
    {
        /** @var string $model */
        $model = $this->model();

        /** @var Model $model */
        $model = new $model();

        $this->assertEqualsCanonicalizing($expectedTable, $model->getTable());
    }
}
