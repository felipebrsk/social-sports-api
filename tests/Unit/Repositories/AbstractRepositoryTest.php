<?php

namespace Tests\Unit\Repositories;

use Mockery;
use Tests\TestCase;
use Mockery\MockInterface;
use App\Repositories\AbstractRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Contracts\Filters\CriterionFilterInterface;
use Illuminate\Database\Eloquent\{
    Model,
    Builder,
    Collection,
};

/**
 * Stub class to test the AbstractRepository.
 *
 * @extends AbstractRepository<Model>
 */
class StubRepository extends AbstractRepository
{
    /**
     * {@inheritDoc}
     */
    protected array $allowedFilters = [
        'type',
        'status',
    ];

    /**
     * {@inheritDoc}
     */
    protected array $allowedSorts = [
        'id',
        'created_at',
    ];

    /**
     * {@inheritDoc}
     */
    protected string $defaultSortBy = 'created_at';

    /**
     * {@inheritDoc}
     */
    protected string $defaultSortOrder = 'asc';

    /**
     * {@inheritDoc}
     */
    protected ?int $defaultLimit = 50;

    /**
     * {@inheritDoc}
     */
    protected int $defaultPerPage = 15;
}

class AbstractRepositoryTest extends TestCase
{
    /**
     * The model mock.
     *
     * @var Model&MockInterface
     */
    private Model&MockInterface $model;

    /**
     * The builder mock.
     *
     * @var Builder<Model>&MockInterface
     */
    private Builder&MockInterface $builder;

    /**
     * The repository instance.
     *
     * @var StubRepository
     */
    private StubRepository $repository;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->model = Mockery::mock(Model::class);
        $this->builder = Mockery::mock(Builder::class);

        $this->repository = new StubRepository($this->model);
    }

    /**
     * Test if repository returns default configuration getters correctly.
     *
     * @return void
     */
    public function test_if_returns_default_configuration_getters_correctly(): void
    {
        $this->assertEquals(['type', 'status'], $this->repository->getAllowedFilters());
        $this->assertEquals(['id', 'created_at'], $this->repository->getAllowedSorts());
        $this->assertEquals('created_at', $this->repository->getDefaultSortBy());
        $this->assertEquals('asc', $this->repository->getDefaultSortOrder());
        $this->assertEquals(15, $this->repository->getDefaultPerPage());
        $this->assertEquals(50, $this->repository->getDefaultLimit());
    }

    /**
     * Test if magic call delegates to builder and resets scope.
     *
     * @return void
     */
    public function test_if_magic_call_delegates_to_builder_and_resets_scope(): void
    {
        $this->model
            ->shouldReceive('newQuery')
            ->once()
            ->andReturn($this->builder);

        $this->builder
            ->shouldReceive('count')
            ->once()
            ->andReturn(10);

        $result = $this->repository->count();

        $this->assertEquals(10, $result);
    }

    /**
     * Test if criteria is applied and scopes are built correctly.
     *
     * @return void
     */
    public function test_if_criteria_is_applied_and_scopes_are_built_correctly(): void
    {
        $criterion = Mockery::mock(CriterionFilterInterface::class);
        $collection = Mockery::mock(Collection::class);

        $this->model
            ->shouldReceive('newQuery')
            ->once()
            ->andReturn($this->builder);

        $this->builder
            ->shouldReceive('with')
            ->once()
            ->with(['relations'])
            ->andReturnSelf();

        $this->builder
            ->shouldReceive('select')
            ->once()
            ->with(['id', 'name'])
            ->andReturnSelf();

        $this->builder
            ->shouldReceive('withCount')
            ->once()
            ->with(['comments'])
            ->andReturnSelf();

        $criterion
            ->shouldReceive('apply')
            ->once()
            ->with($this->builder)
            ->andReturn($this->builder);

        $this->builder
            ->shouldReceive('get')
            ->once()
            ->andReturn($collection);

        $result = $this->repository
            ->withRelations(['relations'])
            ->select(['id', 'name'])
            ->withCount(['comments'])
            ->withCriteria($criterion)
            ->all();

        $this->assertSame($collection, $result);
    }

    /**
     * Test if can paginate results.
     *
     * @return void
     */
    public function test_if_can_paginate_results(): void
    {
        $paginator = Mockery::mock(LengthAwarePaginator::class);

        $this->model
            ->shouldReceive('newQuery')
            ->once()
            ->andReturn($this->builder);

        $this->builder
            ->shouldReceive('paginate')
            ->once()
            ->with(20)
            ->andReturn($paginator);

        $paginator
            ->shouldReceive('withQueryString')
            ->once()
            ->andReturnSelf();

        $result = $this->repository->paginate(20);

        $this->assertSame($paginator, $result);
    }

    /**
     * Test if can create a new record.
     *
     * @return void
     */
    public function test_if_can_create_a_new_record(): void
    {
        $data = ['name' => 'Test Name'];
        $createdModel = Mockery::mock(Model::class);

        $this->model
            ->shouldReceive('create')
            ->once()
            ->with($data)
            ->andReturn($createdModel);

        $result = $this->repository->create($data);

        $this->assertSame($createdModel, $result);
    }

    /**
     * Test if can find a record by id.
     *
     * @return void
     */
    public function test_if_can_find_a_record_by_id(): void
    {
        $foundModel = Mockery::mock(Model::class);
        $id = 1;

        $this->model
            ->shouldReceive('newQuery')
            ->once()
            ->andReturn($this->builder);

        $this->builder
            ->shouldReceive('find')
            ->once()
            ->with($id)
            ->andReturn($foundModel);

        $result = $this->repository->find($id);

        $this->assertSame($foundModel, $result);
    }

    /**
     * Test if can update a record.
     *
     * @return void
     */
    public function test_if_can_update_a_record(): void
    {
        $id = 1;
        $data = ['name' => 'Updated Name'];
        $foundModel = Mockery::mock(Model::class);

        $this->model
            ->shouldReceive('newQuery')
            ->once()
            ->andReturn($this->builder);

        $this->builder
            ->shouldReceive('select')
            ->once()
            ->with(['id'])
            ->andReturnSelf();

        $this->builder
            ->shouldReceive('findOrFail')
            ->once()
            ->with($id)
            ->andReturn($foundModel);

        $foundModel
            ->shouldReceive('update')
            ->once()
            ->with($data)
            ->andReturnTrue();

        $foundModel
            ->shouldReceive('load')
            ->once()
            ->with([]) // Default $with is empty array
            ->andReturnSelf();

        $result = $this->repository->update($data, $id);

        $this->assertSame($foundModel, $result);
    }

    /**
     * Test if can delete a record by id.
     *
     * @return void
     */
    public function test_if_can_delete_a_record_by_id(): void
    {
        $id = 1;

        $this->model
            ->shouldReceive('newQuery')
            ->once()
            ->andReturn($this->builder);

        $this->builder
            ->shouldReceive('where')
            ->once()
            ->with('id', $id)
            ->andReturnSelf();

        $this->builder
            ->shouldReceive('delete')
            ->once()
            ->andReturn(1);

        $this->repository->delete($id);
    }

    /**
     * Test if can delete by instance.
     *
     * @return void
     */
    public function test_if_can_delete_by_instance(): void
    {
        $modelInstance = Mockery::mock(Model::class);

        $modelInstance
            ->shouldReceive('delete')
            ->once()
            ->andReturn(true);

        $this->repository->deleteByInstance($modelInstance);
    }

    /**
     * Test if can bulk delete.
     *
     * @return void
     */
    public function test_if_can_bulk_delete(): void
    {
        $ids = [1, 2, 3, 4, 5];

        $this->model
            ->shouldReceive('getKeyName')
            ->once()
            ->andReturn('id');

        $this->model
            ->shouldReceive('newQuery')
            ->once()
            ->andReturn($this->builder);

        $this->builder
            ->shouldReceive('whereIn')
            ->once()
            ->with('id', $ids)
            ->andReturn($this->builder);

        $this->builder
            ->shouldReceive('delete')
            ->once()
            ->andReturn(count($ids));

        $this->repository->bulkDelete($ids);
    }

    /**
     * Test if can find or fail by given column.
     *
     * @return void
     */
    public function test_if_can_find_or_fail_by_given_column(): void
    {
        $foundModel = Mockery::mock(Model::class);

        $field = 'email';
        $value = 'test@example.com';

        $this->model
            ->shouldReceive('newQuery')
            ->once()
            ->andReturn($this->builder);

        $this->builder
            ->shouldReceive('where')
            ->once()
            ->with($field, $value)
            ->andReturnSelf();

        $this->builder
            ->shouldReceive('firstOrFail')
            ->once()
            ->andReturn($foundModel);

        $result = $this->repository->findOrFailBy($field, $value);

        $this->assertSame($foundModel, $result);
    }

    /**
     * Test if can find all records matching a specific field value.
     *
     * @return void
     */
    public function test_if_can_find_all_by_given_column(): void
    {
        $collection = Mockery::mock(Collection::class);

        $field = 'status';
        $value = 'active';

        $this->model
            ->shouldReceive('newQuery')
            ->once()
            ->andReturn($this->builder);

        $this->builder
            ->shouldReceive('where')
            ->once()
            ->with($field, $value)
            ->andReturnSelf();

        $this->builder
            ->shouldReceive('get')
            ->once()
            ->andReturn($collection);

        $result = $this->repository->findAllBy($field, $value);

        $this->assertSame($collection, $result);
    }

    /**
     * Test if can update or create a record.
     *
     * @return void
     */
    public function test_if_can_update_or_create_a_record(): void
    {
        $values = ['name' => 'New Name'];
        $attributes = ['email' => 'test@example.com'];

        $returnedModel = Mockery::mock(Model::class);

        $this->model
            ->shouldReceive('updateOrCreate')
            ->once()
            ->with($attributes, $values)
            ->andReturn($returnedModel);

        $result = $this->repository->updateOrCreate($attributes, $values);

        $this->assertSame($returnedModel, $result);
    }

    /**
     * Test if can find or create a record.
     *
     * @return void
     */
    public function test_if_can_first_or_create_a_record(): void
    {
        $values = ['name' => 'Initial Name'];
        $attributes = ['email' => 'test@example.com'];

        $returnedModel = Mockery::mock(Model::class);

        $this->model
            ->shouldReceive('firstOrCreate')
            ->once()
            ->with($attributes, $values)
            ->andReturn($returnedModel);

        $result = $this->repository->firstOrCreate($attributes, $values);

        $this->assertSame($returnedModel, $result);
    }

    /**
     * Test if can find a specific record by field and value without throwing an exception.
     *
     * @return void
     */
    public function test_if_can_find_by_given_column(): void
    {
        $foundModel = Mockery::mock(Model::class);

        $field = 'slug';
        $value = 'test-slug';

        $this->model
            ->shouldReceive('newQuery')
            ->once()
            ->andReturn($this->builder);

        $this->builder
            ->shouldReceive('where')
            ->once()
            ->with($field, $value)
            ->andReturnSelf();

        $this->builder
            ->shouldReceive('first')
            ->once()
            ->andReturn($foundModel);

        $result = $this->repository->findBy($field, $value);

        $this->assertSame($foundModel, $result);
    }

    /**
     * Test if can find records matching an array of values inside a column.
     *
     * @return void
     */
    public function test_if_can_find_in_given_column_values(): void
    {
        $collection = Mockery::mock(Collection::class);

        $field = 'id';
        $ids = [1, 2, 3];

        $this->model
            ->shouldReceive('newQuery')
            ->once()
            ->andReturn($this->builder);

        $this->builder
            ->shouldReceive('whereIn')
            ->once()
            ->with($field, $ids)
            ->andReturnSelf();

        $this->builder
            ->shouldReceive('get')
            ->once()
            ->andReturn($collection);

        $result = $this->repository->findIn($field, $ids);

        $this->assertSame($collection, $result);
    }

    /**
     * Test if can find a record or fail using its ID.
     *
     * @return void
     */
    public function test_if_can_find_or_fail_by_id(): void
    {
        $id = 99;

        $foundModel = Mockery::mock(Model::class);

        $this->model
            ->shouldReceive('newQuery')
            ->once()
            ->andReturn($this->builder);

        $this->builder
            ->shouldReceive('findOrFail')
            ->once()
            ->with($id)
            ->andReturn($foundModel);

        $result = $this->repository->findOrFail($id);

        $this->assertSame($foundModel, $result);
    }

    /**
     * Test if upsert passes data straight to the model layer.
     *
     * @return void
     */
    public function test_if_can_upsert_records(): void
    {
        $values = [
            ['email' => 'a@test.com', 'name' => 'A'],
            ['email' => 'b@test.com', 'name' => 'B']
        ];
        $uniqueBy = ['email'];
        $update = ['name'];

        $this->model
            ->shouldReceive('upsert')
            ->once()
            ->with($values, $uniqueBy, $update)
            ->andReturnNull();

        $this->repository->upsert($values, $uniqueBy, $update);

        $this->assertTrue(true); // @phpstan-ignore-line
    }

    /**
     * Test if can get all records with filters, sorting, and limit applied.
     *
     * @return void
     */
    public function test_if_can_get_all_with_filters(): void
    {
        $collection = Mockery::mock(Collection::class);

        $params = [
            'filter_by' => ['type' => 'admin', 'invalid_filter' => 'hack'],
            'sort_by' => 'created_at',
            'sort_order' => 'asc',
            'limit' => 10,
        ];

        $this->model
            ->shouldReceive('newQuery')
            ->once()
            ->andReturn($this->builder);

        $this->builder
            ->shouldReceive('where')
            ->once()
            ->with('type', 'LIKE', '%admin%')
            ->andReturnSelf();

        $this->builder
            ->shouldNotReceive('where')
            ->with('invalid_filter', 'LIKE', '%hack%')
            ->andReturnSelf();

        $this->builder
            ->shouldReceive('orderBy')
            ->once()
            ->with('created_at', 'asc')
            ->andReturnSelf();

        $this->builder
            ->shouldReceive('limit')
            ->once()
            ->with(10)
            ->andReturnSelf();

        $this->builder
            ->shouldReceive('get')
            ->once()
            ->andReturn($collection);

        $result = $this->repository->allWithFilters($params);

        $this->assertSame($collection, $result);
    }

    /**
     * Test if can paginate records with filters and sorting applied.
     *
     * @return void
     */
    public function test_if_can_paginate_with_filters(): void
    {
        $paginator = Mockery::mock(LengthAwarePaginator::class);

        $params = [
            'filter_by' => ['status' => 'active'],
            'sort_by' => 'id',
            'sort_order' => 'desc',
            'per_page' => 15,
        ];

        $this->model
            ->shouldReceive('newQuery')
            ->once()
            ->andReturn($this->builder);

        $this->builder
            ->shouldReceive('where')
            ->once()
            ->with('status', 'LIKE', '%active%')
            ->andReturnSelf();

        $this->builder
            ->shouldReceive('orderBy')
            ->once()
            ->with('id', 'desc')
            ->andReturnSelf();

        $this->builder
            ->shouldReceive('paginate')
            ->once()
            ->with(15)
            ->andReturn($paginator);

        $paginator
            ->shouldReceive('withQueryString')
            ->once()
            ->andReturnSelf();

        $result = $this->repository->paginateWithFilters($params);

        $this->assertSame($paginator, $result);
    }
}
