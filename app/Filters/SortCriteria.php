<?php

namespace App\Filters;

use App\Contracts\Filters\CriterionFilterInterface;
use Illuminate\Database\Eloquent\{
    Model,
    Builder,
};

use function in_array;

/**
 * @template TModel of Model
 * @implements CriterionFilterInterface<TModel>
 */
class SortCriteria implements CriterionFilterInterface
{
    /**
     * Create a new filter instance.
     *
     * @param string $sortBy
     * @param string $sortOrder
     * @param list<string> $allowedColumns
     * @return void
     */
    public function __construct(
        protected string $sortBy = 'created_at',
        protected string $sortOrder = 'desc',
        protected array $allowedColumns = [],
    ) {
        //
    }

    /**
     * {@inheritDoc}
     */
    public function apply(Builder $query): Builder
    {
        if (in_array($this->sortBy, $this->allowedColumns, true)) {
            $direction = strtolower($this->sortOrder) === 'asc' ? 'asc' : 'desc';

            $query->orderBy($this->sortBy, $direction);
        }

        return $query;
    }

    /**
     * Get the column to sort by.
     *
     * @return string
     */
    public function getSortBy(): string
    {
        return $this->sortBy;
    }

    /**
     * Get the sort order.
     *
     * @return string
     */
    public function getSortOrder(): string
    {
        return $this->sortOrder;
    }
}
