<?php

namespace App\Filters;

use Closure;
use App\Contracts\Filters\CriterionFilterInterface;
use Illuminate\Database\Eloquent\{
    Model,
    Builder,
};

class WhereHasCriteria implements CriterionFilterInterface
{
    /**
     * Create a new filter instance.
     *
     * @param string $relation
     * @param (Closure(Builder<Model>): mixed)|null  $callback
     * @return void
     */
    public function __construct(
        private readonly string $relation,
        private readonly ?Closure $callback = null,
    ) {
        //
    }

    /**
     * {@inheritDoc}
     */
    public function apply(Builder $apply): Builder
    {
        return $apply->whereHas($this->relation, $this->callback);
    }
}
