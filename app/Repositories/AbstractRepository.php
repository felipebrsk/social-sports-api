<?php

namespace App\Repositories;

use App\Contracts\Filters\CriterionFilterInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Contracts\Repositories\AbstractRepositoryInterface;
use App\Filters\{
    SortCriteria,
    LimitCriteria,
    FilterCriteria,
};
use Illuminate\Database\Eloquent\{
    Model,
    Builder,
    Collection,
};

/**
 * @template TModel of Model
 * @implements AbstractRepositoryInterface<TModel>
 *
 * @method int count()
 * @method bool exists()
 * @method bool insert(array<mixed> $values)
 * @method mixed value(string|\Illuminate\Contracts\Database\Query\Expression $column)
 * @method TModel|null first(string[]|string $columns = ['*'])
 * @method Collection<int, TModel> get(string[]|string $columns = ['*'])
 * @method TModel|static firstOrNew(array<string, mixed> $attributes, array<string, mixed> $values = [])
 */
abstract class AbstractRepository implements AbstractRepositoryInterface
{
    /**
     * The related model.
     *
     * @var TModel
     */
    protected Model $model;

    /**
     * The relations to eager load.
     *
     * @var array<string>
     */
    protected array $with = [];

    /**
     * The relation counts to eager load.
     *
     * @var array<string>
     */
    protected array $withCount = [];

    /**
     * The columns to select.
     *
     * @var array<string>
     */
    protected array $select = [];

    /**
     * The criterias for the filters.
     *
     * @var array<CriterionFilterInterface>
     */
    protected array $criteria = [];

    /**
     * The allowed filters.
     *
     * @var list<string>
     */
    protected array $allowedFilters = [];

    /**
     * The allowed sorts.
     *
     * @var list<string>
     */
    protected array $allowedSorts = [];

    /**
     * The default sort by.
     *
     * @var string
     */
    protected string $defaultSortBy = 'id';

    /**
     * The default sort order.
     *
     * @var string
     */
    protected string $defaultSortOrder = 'desc';

    /**
     * The default limit.
     *
     * @var int|null
     */
    protected ?int $defaultLimit = null;

    /**
     * The default per page.
     *
     * @var int
     */
    protected int $defaultPerPage = 20;

    /**
     * Create a new abstract repository instance.
     *
     * @param TModel $model
     * @return void
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * {@inheritDoc}
     */
    public function resetScope(): static
    {
        $this->with = [];
        $this->select = [];
        $this->criteria = [];
        $this->withCount = [];

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function withCriteria(CriterionFilterInterface ...$criteria): static
    {
        $this->criteria = [...$this->criteria, ...$criteria];

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function withRelations(array $relations): static
    {
        $this->with = $relations;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function withCount(array $relations): static
    {
        $this->withCount = $relations;

        return $this;
    }

    /**
     * Creates a new query builder instance and applies eager loading.
     *
     * @return Builder<TModel>
     */
    protected function newQuery(): Builder
    {
        /** @var Builder<TModel> $query */
        $query = $this->model->newQuery();

        if (!empty($this->with)) {
            $query->with($this->with);
        }

        if (!empty($this->select)) {
            $query->select($this->select);
        }

        if (!empty($this->withCount)) {
            $query->withCount($this->withCount);
        }

        return $query;
    }

    /**
     * Dynamically handle calls to methods on the repository.
     *
     * @param string $method
     * @param list<mixed> $arguments
     * @return mixed
     */
    public function __call(string $method, array $arguments): mixed
    {
        $builder = $this->applyCriteria();

        // Calls the wished method on builder (e.g.: count, sum, exists, etc.)
        $result = $builder->{$method}(...$arguments);

        $this->resetScope();

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function select(array $columns): static
    {
        $this->select = $columns;

        return $this;
    }

    /**
     * Apply all criteria to the query builder.
     *
     * @return Builder<TModel>
     */
    protected function applyCriteria(): Builder
    {
        $query = $this->newQuery();

        foreach ($this->criteria as $criterion) {
            $query = $criterion->apply($query);
        }

        return $query;
    }

    /**
     * {@inheritDoc}
     */
    public function paginate(?int $perPage = null): LengthAwarePaginator
    {
        $perPage = $perPage ?? $this->defaultPerPage;

        $results = $this->applyCriteria()->paginate($perPage)->withQueryString();

        $this->resetScope();

        return $results;
    }

    /**
     * {@inheritDoc}
     */
    public function paginateWithFilters(array $params): LengthAwarePaginator
    {
        /** @var array<string, string|null> $filters */
        $filters = array_intersect_key((array) ($params['filter_by'] ?? []), array_flip($this->allowedFilters));

        /** @var string $sortBy */
        $sortBy = $params['sort_by'] ?? $this->defaultSortBy;

        /** @var int $perPage */
        $perPage = $params['per_page'] ?? $this->defaultPerPage;

        /** @var string $sortOrder */
        $sortOrder = $params['sort_order'] ?? $this->defaultSortOrder;

        $this->withCriteria(
            new FilterCriteria($filters, $this->allowedFilters),
            new SortCriteria($sortBy, $sortOrder, $this->allowedSorts)
        );

        return $this->paginate($perPage);
    }

    /**
     * {@inheritDoc}
     */
    public function allWithFilters(array $params): Collection
    {
        /** @var array<string, string|null> $filters */
        $filters = array_intersect_key((array) ($params['filter_by'] ?? []), array_flip($this->allowedFilters));

        /** @var string $sortBy */
        $sortBy = $params['sort_by'] ?? $this->defaultSortBy;

        /** @var string $sortOrder */
        $sortOrder = $params['sort_order'] ?? $this->defaultSortOrder;

        /** @var int|null $limit */
        $limit = $params['limit'] ?? $this->defaultLimit;

        $this->withCriteria(
            new FilterCriteria($filters, $this->allowedFilters),
            new SortCriteria($sortBy, $sortOrder, $this->allowedSorts),
            new LimitCriteria($limit),
        );

        return $this->all();
    }

    /**
     * {@inheritDoc}
     */
    public function getAllowedFilters(): array
    {
        return $this->allowedFilters;
    }

    /**
     * {@inheritDoc}
     */
    public function getAllowedSorts(): array
    {
        return $this->allowedSorts;
    }

    /**
     * {@inheritDoc}
     */
    public function getDefaultSortBy(): string
    {
        return $this->defaultSortBy;
    }

    /**
     * {@inheritDoc}
     */
    public function getDefaultSortOrder(): string
    {
        return $this->defaultSortOrder;
    }

    /**
     * {@inheritDoc}
     */
    public function getDefaultPerPage(): int
    {
        return $this->defaultPerPage;
    }

    /**
     * {@inheritDoc}
     */
    public function getDefaultLimit(): ?int
    {
        return $this->defaultLimit;
    }

    /**
     * {@inheritDoc}
     */
    public function all(): Collection
    {
        /** @var Collection<int, TModel> $results */
        $results = $this->applyCriteria()->get();

        $this->resetScope();

        return $results;
    }

    /**
     * {@inheritDoc}
     */
    public function create(array $data): Model
    {
        /** @var TModel $model */
        $model = $this->model->create($data);

        return $model;
    }

    /**
     * {@inheritDoc}
     */
    public function firstOrCreate(array $attributes, array $values = []): Model
    {
        /** @var TModel $model */
        $model = $this->model->firstOrCreate($attributes, $values);

        return $model;
    }

    /**
     * {@inheritDoc}
     */
    public function updateOrCreate(array $attributes, array $values = []): Model
    {
        /** @var TModel $model */
        $model = $this->model->updateOrCreate($attributes, $values);

        return $model;
    }

    /**
     * {@inheritDoc}
     */
    public function find(mixed $id): ?Model
    {
        /** @var TModel|null $result */
        $result = $this->applyCriteria()->find($id);

        $this->resetScope();

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function findBy(string $field, mixed $value): ?Model
    {
        /** @var TModel|null $result */
        $result = $this->applyCriteria()->where($field, $value)->first();

        $this->resetScope();

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function findAllBy(string $field, mixed $value): Collection
    {
        /** @var Collection<int, TModel> $results */
        $results = $this->applyCriteria()->where($field, $value)->get();

        $this->resetScope();

        return $results;
    }

    /**
     * {@inheritDoc}
     */
    public function findIn(string $field, array $ids): Collection
    {
        /** @var Collection<int, TModel> $results */
        $results = $this->applyCriteria()->whereIn($field, $ids)->get();

        $this->resetScope();

        return $results;
    }

    /**
     * {@inheritDoc}
     */
    public function findOrFail(mixed $id): Model
    {
        /** @var TModel $result */
        $result = $this->applyCriteria()->findOrFail($id);

        $this->resetScope();

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function findOrFailBy(string $field, mixed $value): Model
    {
        /** @var TModel $result */
        $result = $this->applyCriteria()->where($field, $value)->firstOrFail();

        $this->resetScope();

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function update(array $data, mixed $id): Model
    {
        /** @var TModel $model */
        $model = $this->select([
            'id',
        ])->findOrFail($id);

        $model->update($data);

        return $model->load($this->with);
    }

    /**
     * {@inheritDoc}
     */
    public function upsert(array $values, array $uniqueBy, ?array $update = null): void
    {
        $this->model->upsert($values, $uniqueBy, $update);
    }

    /**
     * {@inheritDoc}
     */
    public function delete(mixed $id): void
    {
        $this->model->newQuery()->where('id', $id)->delete();
    }

    /**
     * {@inheritDoc}
     */
    public function bulkDelete(array $ids): int
    {
        /** @var int */
        return $this->model->newQuery()->whereIn($this->model->getKeyName(), $ids)->delete();
    }

    /**
     * {@inheritDoc}
     */
    public function deleteByInstance(Model $model): void
    {
        $model->delete();
    }
}
