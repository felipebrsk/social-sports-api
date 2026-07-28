<?php

namespace App\Contracts\Repositories;

use Closure;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\{
    Model,
    Collection,
};
use App\Contracts\Filters\{
    CriterionFilterInterface,
    FilterableRepositoryInterface,
};

/**
 * @template TModel of Model
 *
 * @method int count()
 * @method bool exists()
 * @method bool insert(array<mixed> $values)
 * @method mixed value(string|\Illuminate\Contracts\Database\Query\Expression $column)
 * @method TModel|null first(string[]|string $columns = ['*'])
 * @method Collection<int, TModel> get(string[]|string $columns = ['*'])
 * @method TModel|static firstOrNew(array<string, mixed> $attributes, array<string, mixed> $values = [])
 */
interface AbstractRepositoryInterface extends FilterableRepositoryInterface
{
    /**
     * Accumulates the criteria to be applied.
     *
     * @param CriterionFilterInterface ...$criteria
     * @return static
     */
    public function withCriteria(CriterionFilterInterface ...$criteria): static;

    /**
     * Set the relations to eager load.
     *
     * @param array<string> $relations
     * @return static
     */
    public function withRelations(array $relations): static;

    /**
     * Set the counts to eager load.
     *
     * @param array<int|string, (Closure)|string> $relations
     * @return static
     */
    public function withCount(array $relations): static;

    /**
     * Resets the query scope by clearing criteria and relations.
     * Call this after a query is executed to avoid state pollution.
     *
     * @return static
     */
    public function resetScope(): static;

    /**
     * Set the columns to be selected.
     *
     * @param array<string> $columns
     * @return static
     */
    public function select(array $columns): static;

    /**
     * Paginate the results with filters.
     *
     * @param array<string, mixed> $params
     * @return LengthAwarePaginator<int, TModel>
     */
    public function paginateWithFilters(array $params): LengthAwarePaginator;

    /**
     * Get all results with filters.
     *
     * @param array<string, mixed> $params
     * @return Collection<int, TModel>
     */
    public function allWithFilters(array $params): Collection;

    /**
     * Paginates the result after aplying the criterias.
     *
     * @param int|null $perPage
     * @return LengthAwarePaginator<int, TModel>
     */
    public function paginate(?int $perPage = null): LengthAwarePaginator;

    /**
     * Should have method all.
     *
     * @return Collection<int, TModel>
     */
    public function all(): Collection;

    /**
     * Should have the create method.
     *
     * @param array<string, mixed> $data
     * @return TModel
     */
    public function create(array $data): Model;

    /**
     * Get first model record or create based on given attributes.
     *
     * @param array<string, mixed> $attributes
     * @param array<string, mixed> $values
     * @return TModel
     */
    public function firstOrCreate(array $attributes, array $values = []): Model;

    /**
     * Update or create model record based on given attributes.
     *
     * @param array<string, mixed> $attributes
     * @param array<string, mixed> $values
     * @return TModel
     */
    public function updateOrCreate(array $attributes, array $values = []): Model;

    /**
     * Should have method find.
     *
     * @param mixed $id
     * @return ?TModel
     */
    public function find(mixed $id): ?Model;

    /**
     * Find a model collection where in array.
     *
     * @param string $field
     * @param array<int, mixed> $ids
     * @return Collection<int, TModel>
     */
    public function findIn(string $field, array $ids): Collection;

    /**
     * Find a mode by a given field.
     *
     * @param string $field
     * @param mixed $value
     * @return ?TModel
     */
    public function findBy(string $field, mixed $value): ?Model;

    /**
     * Find all models by given field.
     *
     * @param string $field
     * @param mixed $value
     * @return Collection<int, TModel>
     */
    public function findAllBy(string $field, mixed $value): Collection;

    /**
     * Should have method find or fail.
     *
     * @param mixed $id
     * @return TModel
     */
    public function findOrFail(mixed $id): Model;

    /**
     * Find or fail a model record by given field.
     *
     * @param string $field
     * @param mixed $value
     * @return TModel
     */
    public function findOrFailBy(string $field, mixed $value): Model;

    /**
     * Should have update method.
     *
     * @param array<string, mixed> $data
     * @param mixed $id
     * @return TModel
     */
    public function update(array $data, mixed $id): Model;

    /**
     * Insert new records or update the existing ones.
     *
     * @param array<int, array<string, mixed>> $values
     * @param list<string> $uniqueBy
     * @param list<string>|null $update
     * @return void
     */
    public function upsert(array $values, array $uniqueBy, ?array $update = null): void;

    /**
     * Should have delete method.
     *
     * @param mixed $id
     * @return void
     */
    public function delete(mixed $id): void;

    /**
     * Delete a batch of records by their PKs.
     *
     * @param list<int|string> $ids
     * @return int
     */
    public function bulkDelete(array $ids): int;

    /**
     * Delete a model by given instance.
     *
     * @param Model $model
     * @return void
     */
    public function deleteByInstance(Model $model): void;
}
