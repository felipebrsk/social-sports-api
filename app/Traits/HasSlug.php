<?php

declare(strict_types=1);

namespace App\Traits;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\{
    Model,
    Builder,
};

/**
 * Trait HasSlug
 *
 * Automatically generates and assigns a unique slug to the model based on configurable columns.
 *
 * @package App\Traits
 * @property bool $exists
 * @mixin Model
 * @method static void saving(\Closure|callable|string $callback)
 */
trait HasSlug
{
    /**
     * Intelephense / PHPStan requirements.
     * These abstract methods ensure static analyzers know the underlying Model methods exist
     * without creating signature conflicts with Eloquent's base Model.
     * 
     * @param string $key
     * @return mixed
     */
    abstract public function getAttribute($key);

    /**
     * @param string $key
     * @param mixed $value
     * @return mixed
     */
    abstract public function setAttribute($key, $value);

    /**
     * @return string
     */
    abstract public function getKeyName();

    /**
     * @return mixed
     */
    abstract public function getKey();

    /**
     * @return Builder
     */
    abstract public function newQuery();

    /**
     * Boot the trait to hook into Eloquent's saving event.
     *
     * @return void
     */
    protected static function bootHasSlug(): void
    {
        static::saving(static function (Model $model): void {
            /** @var self $model */
            $model->generateAndSetSlug();
        });
    }

    /**
     * Define the source column(s) for the slug.
     *
     * @return array<int, string>
     */
    protected function getSlugSourceColumns(): array
    {
        return ['name'];
    }

    /**
     * Define the destination column where the slug will be saved.
     *
     * @return string
     */
    protected function getSlugDestinationColumn(): string
    {
        return 'slug';
    }

    /**
     * Determine if the slug should be regenerated when the model is updated.
     *
     * @return bool
     */
    protected function shouldUpdateSlugOnSave(): bool
    {
        return false;
    }

    /**
     * Generate and set the unique slug for the model.
     *
     * @return void
     */
    protected function generateAndSetSlug(): void
    {
        $destination = $this->getSlugDestinationColumn();

        if (! $this->shouldUpdateSlugOnSave() && ! empty($this->getAttribute($destination))) {
            return;
        }

        $sourceString = $this->getSlugSourceString();

        if (empty(trim($sourceString))) {
            return;
        }

        $slug = Str::slug($sourceString);
        $uniqueSlug = $this->makeSlugUnique($slug);

        $this->setAttribute($destination, $uniqueSlug);
    }

    /**
     * Concatenate the values from the source columns.
     *
     * @return string
     */
    private function getSlugSourceString(): string
    {
        /** @var array<int, string> $columns */
        $columns = $this->getSlugSourceColumns();
        $values = [];

        foreach ($columns as $column) {
            $value = $this->getAttribute((string) $column);

            if (is_scalar($value) || $value instanceof \Stringable) {
                $values[] = (string) $value;
            }
        }

        return implode(' ', array_filter($values));
    }

    /**
     * Ensure the generated slug is strictly unique in the database.
     *
     * @param string $slug The base slug to check.
     * @return string
     */
    private function makeSlugUnique(string $slug): string
    {
        $originalSlug = $slug;
        $counter = 1;

        while ($this->slugExists($slug)) {
            $slug = "$originalSlug-$counter";
            $counter++;
        }

        return $slug;
    }

    /**
     * Check if a given slug already exists for another record in the database.
     *
     * @param string $slug The slug to check for existence.
     * @return bool
     */
    private function slugExists(string $slug): bool
    {
        $destination = $this->getSlugDestinationColumn();
        $primaryKey = $this->getKeyName();

        $query = $this->newQuery()->where($destination, $slug);

        if ($this->exists) {
            $query->where($primaryKey, '!=', $this->getKey());
        }

        if (method_exists($this, 'withTrashed')) {
            /** @phpstan-ignore-next-line */
            $query->withTrashed();
        }

        return $query->exists();
    }
}
