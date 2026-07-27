<?php

namespace Tests\Unit\Filters;

use Mockery;
use Tests\TestCase;
use App\Filters\SortCriteria;
use Illuminate\Database\Eloquent\Builder;

class SortCriteriaTest extends TestCase
{
    /**
     * Test if can call sort by when given.
     *
     * @return void
     */
    public function test_if_can_call_sort_by_when_given(): void
    {
        $sortBy = 'id';
        $sortDirection = 'asc';
        $allowed = [
            'id',
        ];

        $builder = Mockery::mock(Builder::class);
        $builder
            ->shouldReceive('orderBy')
            ->once()
            ->with($sortBy, $sortDirection)
            ->andReturnSelf();

        $criteria = new SortCriteria($sortBy, $sortDirection, $allowed);

        $result = $criteria->apply($builder);

        $this->assertSame($builder, $result);
    }

    /**
     * Test if can ignore sorts for disallowed columns.

     * @return void
     */
    public function test_if_can_ignore_sorts_for_disallowed_columns(): void
    {
        $sortBy = 'id';
        $sortDirection = 'asc';
        $allowed = [
            'name',
        ];

        $builder = Mockery::mock(Builder::class);
        $builder
            ->shouldNotReceive('orderBy')
            ->with(Mockery::any(), Mockery::any());

        $criteria = new SortCriteria($sortBy, $sortDirection, $allowed);

        $result = $criteria->apply($builder);

        $this->assertSame($builder, $result);
    }

    /**
     * Test if apply does nothing with default constructor args.
     *
     * @return void
     */
    public function test_if_apply_does_nothing_with_default_constructor_args(): void
    {
        $builder = Mockery::mock(Builder::class);
        $builder->shouldNotReceive('orderBy');

        $criteria = new SortCriteria();

        $result = $criteria->apply($builder);

        $this->assertSame($builder, $result);
    }

    /**
     * Test if can sort descending.
     *
     * @return void
     */
    public function test_if_can_sort_descending(): void
    {
        $sortBy = 'updated_at';
        $sortDirection = 'desc';
        $allowed = [
            'updated_at',
        ];

        $builder = Mockery::mock(Builder::class);
        $builder
            ->shouldReceive('orderBy')
            ->once()
            ->with($sortBy, $sortDirection)
            ->andReturnSelf();

        $criteria = new SortCriteria($sortBy, $sortDirection, $allowed);

        $result = $criteria->apply($builder);

        $this->assertSame($builder, $result);
    }

    /**
     * Test if can get passed sort by attribute.
     *
     * @return void
     */
    public function test_if_can_get_passed_sort_by_attribute(): void
    {
        $sortBy = 'updated_at';

        $criteria = new SortCriteria($sortBy);

        $this->assertSame($sortBy, $criteria->getSortBy());
    }

    /**
     * Test if can get passed sort order attribute.
     *
     * @return void
     */
    public function test_if_can_get_passed_sort_order_attribute(): void
    {
        $sortDirection = 'desc';

        $criteria = new SortCriteria(sortOrder: $sortDirection);

        $this->assertSame($sortDirection, $criteria->getSortOrder());
    }
}
