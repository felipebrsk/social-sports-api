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
class WhereInCriteria implements CriterionFilterInterface
{
    /**
     * Create a new criteria where filter.
     *
     * @param string $field
     * @param array<mixed, mixed> $values
     * @param string $operator
     */
    public function __construct(
        protected string $field,
        protected array $values,
        protected string $operator = 'and',
    ) {
        //
    }

    /**
     * @inheritDoc
     */
    public function apply(Builder $query): Builder
    {
        return $query->whereIn($this->field, $this->values, $this->operator);
    }
}
