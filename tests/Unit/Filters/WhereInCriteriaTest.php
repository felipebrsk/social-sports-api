<?php

namespace Tests\Unit\Filters;

use Mockery;
use Tests\TestCase;
use App\Filters\WhereInCriteria;
use Illuminate\Database\Eloquent\Builder;

class WhereInCriteriaTest extends TestCase
{
    /**
     * Test if can call where in for a valid field and values with operator.
     *
     * @return void
     */
    public function test_if_can_call_where_in_for_a_valid_field_and_values_with_operator(): void
    {
        $field = 'name';
        $operator = 'and';
        $values = [1, 2, 3];

        $builder = Mockery::mock(Builder::class);

        $builder
            ->shouldReceive('whereIn')
            ->once()
            ->with($field, $values, $operator)
            ->andReturnSelf();

        $criteria = new WhereInCriteria($field, $values, $operator);

        $result = $criteria->apply($builder);

        $this->assertSame($builder, $result);
    }

    /**
     * Test if can apply the default operator equals when not given.
     *
     * @return void
     */
    public function test_if_can_apply_the_default_operator_equals_when_not_given(): void
    {
        $field = 'name';
        $values = [1, 2, 3];

        $builder = Mockery::mock(Builder::class);

        $builder
            ->shouldReceive('whereIn')
            ->once()
            ->with($field, $values, 'and')
            ->andReturnSelf();

        $criteria = new WhereInCriteria($field, $values);

        $result = $criteria->apply($builder);

        $this->assertSame($builder, $result);
    }
}
