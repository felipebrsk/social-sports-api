<?php

namespace App\Contracts\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\{
    Model,
    Collection,
};

/**
 * @template TModel of Model
 */
interface AbstractServiceInterface
{
    /**
     * Paginate the results with filters.
     *
     * @param array<string, mixed> $params
     * @return LengthAwarePaginator<int, TModel>
     */
    public function paginateWithFilters(array $params): LengthAwarePaginator;

    /**
     * Paginates the result after aplying the criterias.
     *
     * @param int $perPage
     * @return LengthAwarePaginator<int, TModel>
     */
    public function paginate(?int $perPage = null): LengthAwarePaginator;

    /**
     * Get all results with filters.
     *
     * @param array<string, mixed> $params
     * @return Collection<int, TModel>
     */
    public function allWithFilters(array $params): Collection;

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
     * Find a model collection where in array.
     *
     * @param string $field
     * @param array<int, mixed> $ids
     * @return Collection<int, TModel>
     */
    public function findIn(string $field, array $ids): Collection;

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
     * Insert new records into the database.
     *
     * @param array<mixed> $values
     * @return bool
     */
    public function insert(array $values): bool;

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
     * @param list<int> $ids
     * @return int
     */
    public function bulkDelete(array $ids): int;
}
