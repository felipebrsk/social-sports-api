<?php

namespace App\Filters;

use DateTimeInterface;
use App\Contracts\Filters\CriterionFilterInterface;
use Illuminate\Database\Eloquent\{
    Model,
    Builder,
};

/**
 * @template TModel of Model
 * @implements CriterionFilterInterface<TModel>
 */
class WhereDateCriteria implements CriterionFilterInterface
{
    /**
     * Create a new criteria where filter.
     *
     * @param string $field
     * @param string|DateTimeInterface $value
     * @param string $operator
     */
    public function __construct(
        protected string $field,
        protected mixed $value,
        protected string $operator = '=',
    ) {
        //
    }

    /**
     * {@inheritDoc}
     */
    public function apply(Builder $query): Builder
    {
        return $query->whereDate($this->field, $this->operator, $this->value);
    }
}
