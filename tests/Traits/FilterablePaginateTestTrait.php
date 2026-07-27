<?php

namespace Tests\Traits;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

trait FilterablePaginateTestTrait
{
    /**
     * Get the route for filterable testing.
     */
    abstract protected function getFilterableRoute(): string;

    /**
     * Get the factory for the testable model.
     */
    abstract protected function getFilterableFactory(): Factory;

    /**
     * Get the filterable model class.
     *
     * @return class-string
     */
    abstract protected function getFilterableModel(): string;

    /**
     * Get the column name that can be filtered and ordered.
     * Should be a string column type for test works correctly.
     * E.g.: 'name'
     */
    abstract protected function getFilterableColumn(): string;

    /**
     * Get the default route params.
     *
     * @param array<string, int|string|bool> $overrides
     * @return array<string, int|string|bool>
     */
    abstract protected function getParameters(array $overrides = []): array;

    /**
     * Test if can view a paginated list of resources.
     *
     * @group pagination
     */
    public function test_can_view_a_paginated_list_of_resources(): void
    {
        $this->getFilterableFactory()->count(5)->create();

        $this->withoutMiddleware()->getJson(route($this->getFilterableRoute(), $this->getParameters()))
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                    ],
                ],
                'links',
                'meta',
            ])->assertJsonCount(5, 'data');
    }

    /**
     * Test if can filter by a specific column.
     *
     * @group pagination
     */
    public function test_can_filter_by_a_specific_column(): void
    {
        $column = $this->getFilterableColumn();
        $uniqueValue = 'Value To Be Found - ' . Str::random(10);

        $this->getFilterableFactory()->create([$column => $uniqueValue]);
        $this->getFilterableFactory()->count(3)->create();

        $this->withoutMiddleware()->getJson(route($this->getFilterableRoute(), $this->getParameters([
            "filter_by[$column]" => $uniqueValue,
        ])))->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath("data.0.$column", $uniqueValue);
    }

    /**
     * Test if it returns empty when filtering with a non-existent value.
     *
     * @group pagination
     */
    public function test_returns_empty_when_filtering_with_non_existent_value(): void
    {
        $this->getFilterableFactory()->count(5)->create();

        $this->withoutMiddleware()->getJson(route($this->getFilterableRoute(), $this->getParameters([
            'filter_by[' . $this->getFilterableColumn() . ']' => 'ValueThatDoesNotExist' . Str::random(),
        ])))->assertOk()->assertJsonCount(0, 'data');
    }

    /**
     * Test if can sort by column in ascending order.
     *
     * @group pagination
     */
    public function test_can_sort_by_column_in_ascending_order(): void
    {
        $column = $this->getFilterableColumn();

        $this->getFilterableFactory()->create([$column => 'C Record']);
        $this->getFilterableFactory()->create([$column => 'A Record']);
        $this->getFilterableFactory()->create([$column => 'B Record']);

        $responseValues = $this->withoutMiddleware()->getJson(route($this->getFilterableRoute(), $this->getParameters([
            'sort_by' => $column,
            'sort_order' => 'asc',
        ])))->assertOk()->json("data.*.$column");

        $this->assertEquals(['A Record', 'B Record', 'C Record'], $responseValues);
    }

    /**
     * Test if can sort by column in descending order.
     *
     * @group pagination
     */
    public function test_can_sort_by_column_in_descending_order(): void
    {
        $column = $this->getFilterableColumn();

        $this->getFilterableFactory()->create([$column => 'C Record']);
        $this->getFilterableFactory()->create([$column => 'A Record']);
        $this->getFilterableFactory()->create([$column => 'B Record']);

        $responseValues = $this->withoutMiddleware()->getJson(route($this->getFilterableRoute(), $this->getParameters([
            'sort_by' => $column,
            'sort_order' => 'desc',
        ])))->assertOk()->json("data.*.$column");

        $this->assertEquals(['C Record', 'B Record', 'A Record'], $responseValues);
    }

    /**
     * Test if pagination works correctly.
     *
     * @group pagination
     */
    public function test_pagination_works_correctly(): void
    {
        $this->getFilterableFactory()->count(10)->create();

        $this->withoutMiddleware()
            ->getJson(route($this->getFilterableRoute(), $this->getParameters(['per_page' => 3])))
            ->assertOk()
            ->assertJsonCount(3, 'data')
            ->assertJsonPath('meta.per_page', 3)
            ->assertJsonPath('meta.total', 10)
            ->assertJsonPath('meta.current_page', 1);
    }

    /**
     * Test if it fails validation when sorting by a non-allowed column.
     *
     * @group pagination
     */
    public function test_fails_validation_when_sorting_by_a_non_allowed_column(): void
    {
        $this->withoutMiddleware()
            ->getJson(route($this->getFilterableRoute(), $this->getParameters(['sort_by' => 'some_invalid_column'])))
            ->assertUnprocessable()
            ->assertJsonValidationErrors('sort_by', 'errors');
    }

    /**
     * Test if it fails validation with an invalid sort order.
     *
     * @group pagination
     */
    public function test_fails_validation_with_invalid_sort_order(): void
    {
        $this->withoutMiddleware()
            ->getJson(route($this->getFilterableRoute(), $this->getParameters(['sort_order' => 'invalid_order'])))
            ->assertUnprocessable()
            ->assertJsonValidationErrors('sort_order', 'errors');
    }

    /**
     * Test if it can combine filter, sort and pagination.
     *
     * @group pagination
     */
    public function test_can_combine_filter_sort_and_pagination(): void
    {
        $column = $this->getFilterableColumn();
        $keyword = 'Keyword';

        // Create records that match the filter, in a disordered way
        $this->getFilterableFactory()->create([$column => "C - {$keyword}"]);
        $this->getFilterableFactory()->create([$column => "A - {$keyword}"]);
        $this->getFilterableFactory()->create([$column => "B - {$keyword}"]);

        // Create records that do not match the filter
        $this->getFilterableFactory()->count(5)->create();

        $responseValues = $this->withoutMiddleware()->getJson(route($this->getFilterableRoute(), $this->getParameters([
            "filter_by[$column]" => $keyword,
            'sort_by' => $column,
            'sort_order' => 'asc',
            'per_page' => 2,
        ])))->assertOk()
            ->assertJsonCount(2, 'data') // Should respect the per_page limit
            ->assertJsonPath('meta.total', 3) // Total items that match the filter
            ->json("data.*.$column");

        $this->assertEquals(["A - {$keyword}", "B - {$keyword}"], $responseValues);
    }
}
