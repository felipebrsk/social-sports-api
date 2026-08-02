<?php

namespace App\Filters;

use App\Contracts\Filters\CriterionFilterInterface;
use Illuminate\Database\Eloquent\{
    Model,
    Builder,
};

/**
 * @template TModel of Model
 * @implements CriterionFilterInterface<TModel>
 */
class LimitCriteria implements CriterionFilterInterface
{
    /**
     * Create a new filter instance.
     *
     * @param int|null $limit
     * @return void
     */
    public function __construct(
        protected readonly ?int $limit = null,
    ) {
        //
    }

    /**
     * {@inheritDoc}
     */
    public function apply(Builder $query): Builder
    {
        if ($this->limit) {
            $query->limit($this->limit);
        }

        return $query;
    }
}
