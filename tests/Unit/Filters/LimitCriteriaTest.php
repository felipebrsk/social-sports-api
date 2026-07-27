<?php

namespace Tests\Unit\Filters;

use Mockery;
use Tests\TestCase;
use App\Filters\LimitCriteria;
use Illuminate\Database\Eloquent\Builder;

class LimitCriteriaTest extends TestCase
{
    /**
     * Test if doesn't apply limit if no value was passed.
     *
     * @return void
     */
    public function test_if_doesnt_apply_limit_if_no_value_was_passed(): void
    {
        $criteria = new LimitCriteria();

        $builder = Mockery::mock(Builder::class);
        $builder->shouldNotReceive('limit');

        $query = $criteria->apply($builder);

        $this->assertSame($builder, $query);
    }

    /**
     * Test if can apply limit if value is passed.
     *
     * @return void
     */
    public function test_if_can_apply_limit_if_value_is_passed(): void
    {
        $limit = 100;

        $criteria = new LimitCriteria($limit);

        $builder = Mockery::mock(Builder::class);
        $builder
            ->shouldReceive('limit')
            ->once()
            ->with($limit)
            ->andReturnSelf();

        $query = $criteria->apply($builder);

        $this->assertSame($builder, $query);
    }
}
