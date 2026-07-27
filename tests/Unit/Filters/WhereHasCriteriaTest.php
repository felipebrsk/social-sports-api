<?php

namespace Tests\Unit\Filters;

use Mockery;
use Tests\TestCase;
use App\Filters\WhereHasCriteria;
use Illuminate\Database\Eloquent\Builder;

class WhereHasCriteriaTest extends TestCase
{
    /**
     * Test if can call where has for a valid relation and callback.
     *
     * @return void
     */
    public function test_if_can_call_where_has_for_a_valid_relation_and_callback(): void
    {
        $relation = 'posts';

        $callback = function (Builder $query) {
            $query->where('is_published', true);
        };

        $builder = Mockery::mock(Builder::class);

        $builder
            ->shouldReceive('whereHas')
            ->once()
            ->with($relation, $callback)
            ->andReturnSelf();

        $criteria = new WhereHasCriteria($relation, $callback);

        $result = $criteria->apply($builder);

        $this->assertSame($builder, $result);
    }

    /**
     * Test if can apply the default null callback when not given.
     *
     * @return void
     */
    public function test_if_can_apply_the_default_null_callback_when_not_given(): void
    {
        $relation = 'comments';

        $builder = Mockery::mock(Builder::class);

        $builder
            ->shouldReceive('whereHas')
            ->once()
            ->with($relation, null)
            ->andReturnSelf();

        $criteria = new WhereHasCriteria($relation);

        $result = $criteria->apply($builder);

        $this->assertSame($builder, $result);
    }
}
