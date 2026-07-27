<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;
use App\Contracts\Filters\CriterionFilterInterface;

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
    }

    /**
     * @inheritDoc
     */
    public function apply(Builder $query): Builder
    {
        return $query->whereIn($this->field, $this->values, $this->operator);
    }
}
