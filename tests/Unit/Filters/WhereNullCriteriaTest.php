<?php

namespace Tests\Unit\Filters;

use Mockery;
use Tests\TestCase;
use App\Filters\WhereNullCriteria;
use Illuminate\Database\Eloquent\Builder;

class WhereNullCriteriaTest extends TestCase
{
    /**
     * Test if can call where in for a valid field and values with operator.
     *
     * @return void
     */
    public function test_if_can_call_where_in_for_a_valid_field_and_values_with_operator(): void
    {
        $field = 'name';

        $builder = Mockery::mock(Builder::class);

        $builder
            ->shouldReceive('whereNull')
            ->once()
            ->with($field)
            ->andReturnSelf();

        $criteria = new WhereNullCriteria($field);

        $result = $criteria->apply($builder);

        $this->assertSame($builder, $result);
    }
}
