<?php

namespace App\Filters;

use App\Contracts\Filters\CriterionFilterInterface;
use Illuminate\Contracts\Database\Query\Expression;
use Illuminate\Database\Eloquent\{
    Model,
    Builder,
};

/**
 * @template TModel of Model
 * @implements CriterionFilterInterface<TModel>
 */
class WhereNullCriteria implements CriterionFilterInterface
{
    /**
     * Create a new criteria where filter.
     *
     * @param string|array<int, string>|Expression $columns
     */
    public function __construct(
        protected string|array|Expression $columns,
    ) {
        //
    }

    /**
     * {@inheritDoc}
     */
    public function apply(Builder $query): Builder
    {
        return $query->whereNull($this->columns);
    }
}
