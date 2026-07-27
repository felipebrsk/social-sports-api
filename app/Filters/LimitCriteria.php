<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;
use App\Contracts\Filters\CriterionFilterInterface;

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
