<?php

namespace Tests\Unit\Filters;

use Mockery;
use Tests\TestCase;
use App\Filters\FilterCriteria;
use Illuminate\Database\Eloquent\Builder;

class FilterCriteriaTest extends TestCase
{
    /**
     * Test if can call where for each allowed non null filter.
     *
     * @return void
     */
    public function test_if_can_call_where_for_each_allowed_non_null_filter(): void
    {
        $filters = [
            'name' => 'foo',
            'email' => 'bar',
        ];

        $allowed = [
            'name',
            'email',
        ];

        $builder = Mockery::mock(Builder::class);

        $builder
            ->shouldReceive('where')
            ->once()
            ->with('name', 'LIKE', '%foo%')
            ->andReturnSelf();

        $builder
            ->shouldReceive('where')
            ->once()
            ->with('email', 'LIKE', '%bar%')
            ->andReturnSelf();

        $criteria = new FilterCriteria($filters, $allowed);

        $result = $criteria->apply($builder);

        $this->assertSame($builder, $result);
    }

    /**
     * Test if can apply a LIKE where clause by default.
     *
     * @return void
     */
    public function test_if_can_apply_like_where_by_default(): void
    {
        $filters = ['name' => 'foo'];
        $allowed = ['name'];

        $builder = Mockery::mock(Builder::class);

        $builder
            ->shouldReceive('where')
            ->once()
            ->with('name', 'LIKE', '%foo%')
            ->andReturnSelf();

        $criteria = new FilterCriteria($filters, $allowed);

        $result = $criteria->apply($builder);

        $this->assertSame($builder, $result);
    }

    /**
     * Test if can ignore filters for disallowed columns.
     *
     * @return void
     */
    public function test_if_can_ignore_filters_for_disallowed_columns(): void
    {
        $filters = [
            'name' => 'foo',
            'email' => 'bar',
        ];

        $allowed = [
            'name',
        ];

        $builder = Mockery::mock(Builder::class);

        $builder
            ->shouldReceive('where')
            ->once()
            ->with('name', 'LIKE', '%foo%')
            ->andReturnSelf();

        $builder
            ->shouldNotReceive('where')
            ->with('email', Mockery::any(), Mockery::any());

        $criteria = new FilterCriteria($filters, $allowed);

        $result = $criteria->apply($builder);

        $this->assertSame($builder, $result);
    }

    /**
     * Test if can ignore filters with null values.
     *
     * @return void
     */
    public function test_if_can_ignore_filters_with_null_values(): void
    {
        $filters = [
            'name' => null,
            'email' => 'bar',
        ];

        $allowed = [
            'name',
            'email',
        ];

        $builder = Mockery::mock(Builder::class);

        $builder
            ->shouldReceive('where')
            ->once()
            ->with('email', 'LIKE', '%bar%')
            ->andReturnSelf();

        $builder
            ->shouldNotReceive('where')
            ->with('name', Mockery::any(), Mockery::any());

        $criteria = new FilterCriteria($filters, $allowed);

        $result = $criteria->apply($builder);

        $this->assertSame($builder, $result);
    }

    /**
     * Test if can return builder when has no filters.
     *
     * @return void
     */
    public function test_if_can_return_builder_when_has_no_filters(): void
    {
        $filters = [];
        $allowed = [
            'foo',
        ];

        $builder = Mockery::mock(Builder::class);

        $builder->shouldNotReceive('where');

        $criteria = new FilterCriteria($filters, $allowed);

        $result = $criteria->apply($builder);

        $this->assertSame($builder, $result);
    }

    /**
     * Test if can filter by exact where criteria when given value is integer.
     *
     * @return void
     */
    public function test_if_can_filter_by_exact_where_criteria_when_given_value_is_integer(): void
    {
        $filters = [
            'id' => 23,
        ];

        $allowed = [
            'id',
        ];

        $builder = Mockery::mock(Builder::class);

        $builder
            ->shouldReceive('where')
            ->once()
            ->with('id', '=', $filters['id'])
            ->andReturnSelf();

        $criteria = new FilterCriteria($filters, $allowed);

        $result = $criteria->apply($builder);

        $this->assertSame($builder, $result);
    }

    /**
     * Test if can filter by exact where criteria when given value is boolean.
     *
     * @return void
     */
    public function test_if_can_filter_by_exact_where_criteria_when_given_value_is_boolean(): void
    {
        $filters = [
            'active' => true,
        ];

        $allowed = [
            'active',
        ];

        $builder = Mockery::mock(Builder::class);

        $builder
            ->shouldReceive('where')
            ->once()
            ->with('active', '=', true)
            ->andReturnSelf();

        $criteria = new FilterCriteria($filters, $allowed);

        $result = $criteria->apply($builder);

        $this->assertSame($builder, $result);
    }

    /**
     * Test if can filter by date using whereDate when given value matches YYYY-MM-DD.
     *
     * @return void
     */
    public function test_if_can_filter_by_date_using_where_date_when_given_value_is_date_string(): void
    {
        $filters = [
            'created_at' => '2026-06-08',
        ];

        $allowed = [
            'created_at',
        ];

        $builder = Mockery::mock(Builder::class);

        $builder
            ->shouldReceive('whereDate')
            ->once()
            ->with('created_at', '=', '2026-06-08')
            ->andReturnSelf();

        $builder->shouldNotReceive('where');

        $criteria = new FilterCriteria($filters, $allowed);

        $result = $criteria->apply($builder);

        $this->assertSame($builder, $result);
    }
}
