<?php

namespace App\Contracts\Filters;

use Illuminate\Database\Eloquent\{
    Model,
    Builder,
};

/**
 * @template TModel of Model
 */
interface CriterionFilterInterface
{
    /**
     * Apply the logic to query builder.
     *
     * @param Builder<TModel> $query
     * @return Builder<TModel>
     */
    public function apply(Builder $query): Builder;
}
