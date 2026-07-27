<?php

namespace App\Services;

use App\Contracts\Services\AbstractServiceInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Contracts\Repositories\AbstractRepositoryInterface;
use Illuminate\Database\Eloquent\{
    Model,
    Collection,
};

/**
 * @template TModel of Model
 * @template TRepository of AbstractRepositoryInterface<TModel>
 * @implements AbstractServiceInterface<TModel>
 */
abstract class AbstractService implements AbstractServiceInterface
{
    /**
     * The related repository.
     *
     * @var TRepository
     */
    protected AbstractRepositoryInterface $repository;

    /**
     * Create a new service instance.
     *
     * @param TRepository $repository
     * @return void
     */
    public function __construct(
        AbstractRepositoryInterface $repository,
    ) {
        $this->repository = $repository;
    }

    /**
     * {@inheritDoc}
     */
    public function paginateWithFilters(array $params): LengthAwarePaginator
    {
        return $this->repository->paginateWithFilters($params);
    }

    /**
     * {@inheritDoc}
     */
    public function allWithFilters(array $params): Collection
    {
        return $this->repository->allWithFilters($params);
    }

    /**
     * {@inheritDoc}
     */
    public function paginate(?int $perPage = null): LengthAwarePaginator
    {
        return $this->repository->paginate($perPage);
    }

    /**
     * {@inheritDoc}
     */
    public function all(): Collection
    {
        return $this->repository->all();
    }

    /**
     * {@inheritDoc}
     */
    public function create(array $data): Model
    {
        return $this->repository->create($data);
    }

    /**
     * {@inheritDoc}
     */
    public function update(array $data, mixed $id): Model
    {
        return $this->repository->update($data, $id);
    }

    /**
     * {@inheritDoc}
     */
    public function insert(array $values): bool
    {
        return $this->repository->insert($values);
    }

    /**
     * {@inheritDoc}
     */
    public function upsert(array $values, array $uniqueBy, ?array $update = null): void
    {
        $this->repository->upsert($values, $uniqueBy, $update);
    }

    /**
     * {@inheritDoc}
     */
    public function delete(mixed $id): void
    {
        $this->repository->delete($id);
    }

    /**
     * {@inheritDoc}
     */
    public function firstOrCreate(array $attributes, array $values = []): Model
    {
        return $this->repository->firstOrCreate($attributes, $values);
    }

    /**
     * {@inheritDoc}
     */
    public function updateOrCreate(array $attributes, array $values = []): Model
    {
        return $this->repository->updateOrCreate($attributes, $values);
    }

    /**
     * {@inheritDoc}
     */
    public function find(mixed $id): ?Model
    {
        return $this->repository->find($id);
    }

    /**
     * {@inheritDoc}
     */
    public function findBy(string $field, mixed $value): ?Model
    {
        return $this->repository->findBy($field, $value);
    }

    /**
     * {@inheritDoc}
     */
    public function findAllBy(string $field, mixed $value): Collection
    {
        return $this->repository->findAllBy($field, $value);
    }

    /**
     * {@inheritDoc}
     */
    public function findIn(string $field, array $ids): Collection
    {
        return $this->repository->findIn($field, $ids);
    }

    /**
     * {@inheritDoc}
     */
    public function findOrFail(mixed $id): Model
    {
        return $this->repository->findOrFail($id);
    }

    /**
     * {@inheritDoc}
     */
    public function findOrFailBy(string $field, mixed $value): Model
    {
        return $this->repository->findOrFailBy($field, $value);
    }

    /**
     * {@inheritDoc}
     */
    public function bulkDelete(array $ids): int
    {
        return $this->repository->bulkDelete($ids);
    }
}
