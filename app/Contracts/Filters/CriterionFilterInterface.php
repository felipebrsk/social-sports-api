<?php

namespace App\Contracts\Filters;

use Illuminate\Database\Eloquent\{
    Model,
    Builder,
};

interface CriterionFilterInterface
{
    /**
     * Apply the logic to query builder.
     *
     * @template TModel of Model
     *
     * @param Builder<TModel> $query
     * @return Builder<TModel>
     */
    public function apply(Builder $query): Builder;
}
