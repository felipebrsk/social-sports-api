<?php

namespace Tests\Unit\Filters;

use Mockery;
use Tests\TestCase;
use App\Filters\WhereDateCriteria;
use Illuminate\Database\Eloquent\Builder;

class WhereDateCriteriaTest extends TestCase
{
    /**
     * Test if can call where for a valid field and value with operator.
     *
     * @return void
     */
    public function test_if_can_call_where_for_a_valid_field_and_value_with_operator(): void
    {
        $value = 'foo';
        $field = 'name';
        $operator = '>=';

        $builder = Mockery::mock(Builder::class);

        $builder
            ->shouldReceive('whereDate')
            ->once()
            ->with($field, $operator, $value)
            ->andReturnSelf();

        $criteria = new WhereDateCriteria($field, $value, $operator);

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
        $value = 'foo';
        $field = 'name';

        $builder = Mockery::mock(Builder::class);

        $builder
            ->shouldReceive('whereDate')
            ->once()
            ->with($field, '=', $value)
            ->andReturnSelf();

        $criteria = new WhereDateCriteria($field, $value);

        $result = $criteria->apply($builder);

        $this->assertSame($builder, $result);
    }
}
