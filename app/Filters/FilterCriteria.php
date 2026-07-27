<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;
use App\Contracts\Filters\CriterionFilterInterface;

use function is_int;
use function is_bool;
use function in_array;
use function preg_match;

class FilterCriteria implements CriterionFilterInterface
{
    /**
     * Create a new filter instance.
     *
     * @param array<string, string|int|bool|null> $filters
     * @param list<string> $allowedColumns
     * @return void
     */
    public function __construct(
        protected array $filters,
        protected array $allowedColumns,
    ) {
        //
    }

    /**
     * @inheritDoc
     */
    public function apply(Builder $query): Builder
    {
        foreach ($this->filters as $field => $value) {
            if (in_array($field, $this->allowedColumns, true) && $value !== null) {
                if (is_int($value) || is_bool($value)) {
                    $query->where($field, '=', $value);
                } elseif (preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
                    $query->whereDate($field, '=', $value);
                } else {
                    $query->where($field, 'LIKE', "%$value%");
                }
            }
        }

        return $query;
    }
}
