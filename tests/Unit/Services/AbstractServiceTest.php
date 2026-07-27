<?php

namespace Tests\Unit\Services;

use Mockery;
use Tests\TestCase;
use Mockery\MockInterface;
use App\Services\AbstractService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Contracts\Repositories\AbstractRepositoryInterface;
use Illuminate\Database\Eloquent\{
    Model,
    Collection,
};

/**
 * Stub class to test the AbstractService.
 *
 * @extends AbstractService<Model, AbstractRepositoryInterface<Model>>
 */
class StubService extends AbstractService
{
    //
}

class AbstractServiceTest extends TestCase
{
    /**
     * The repository mock.
     *
     * @var AbstractRepositoryInterface<Model>&MockInterface
     */
    private AbstractRepositoryInterface&MockInterface $repository;

    /**
     * The service instance.
     *
     * @var StubService
     */
    private StubService $service;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = Mockery::mock(AbstractRepositoryInterface::class);

        $this->service = new StubService($this->repository);
    }

    /**
     * Test if can paginate with filters.
     *
     * @return void
     */
    public function test_if_can_paginate_with_filters(): void
    {
        $params = [
            'filter_by' => [
                'name' => 'John',
            ],
        ];

        $paginator = Mockery::mock(LengthAwarePaginator::class);

        $this->repository
            ->shouldReceive('paginateWithFilters')
            ->once()
            ->with($params)
            ->andReturn($paginator);

        $result = $this->service->paginateWithFilters($params);

        $this->assertSame($paginator, $result);
    }

    /**
     * Test if can get all with filters.
     *
     * @return void
     */
    public function test_if_can_get_all_with_filters(): void
    {
        $params = [
            'filter_by' => [
                'status' => 'active',
            ],
        ];

        $collection = Mockery::mock(Collection::class);

        $this->repository
            ->shouldReceive('allWithFilters')
            ->once()
            ->with($params)
            ->andReturn($collection);

        $result = $this->service->allWithFilters($params);

        $this->assertSame($collection, $result);
    }

    /**
     * Test if can paginate.
     *
     * @return void
     */
    public function test_if_can_paginate(): void
    {
        $perPage = 15;

        $paginator = Mockery::mock(LengthAwarePaginator::class);

        $this->repository
            ->shouldReceive('paginate')
            ->once()
            ->with($perPage)
            ->andReturn($paginator);

        $result = $this->service->paginate($perPage);

        $this->assertSame($paginator, $result);
    }

    /**
     * Test if can get all records.
     *
     * @return void
     */
    public function test_if_can_get_all_records(): void
    {
        $collection = Mockery::mock(Collection::class);

        $this->repository
            ->shouldReceive('all')
            ->once()
            ->andReturn($collection);

        $result = $this->service->all();

        $this->assertSame($collection, $result);
    }

    /**
     * Test if can create a new record.
     *
     * @return void
     */
    public function test_if_can_create_a_new_record(): void
    {
        $data = ['name' => 'New User'];

        $model = Mockery::mock(Model::class);

        $this->repository
            ->shouldReceive('create')
            ->once()
            ->with($data)
            ->andReturn($model);

        $result = $this->service->create($data);

        $this->assertSame($model, $result);
    }

    /**
     * Test if can update a record.
     *
     * @return void
     */
    public function test_if_can_update_a_record(): void
    {
        $id = 1;
        $data = [
            'name' => 'Updated User',
        ];

        $model = Mockery::mock(Model::class);

        $this->repository
            ->shouldReceive('update')
            ->once()
            ->with($data, $id)
            ->andReturn($model);

        $result = $this->service->update($data, $id);

        $this->assertSame($model, $result);
    }

    /**
     * Test if can insert multiple records.
     *
     * @return void
     */
    public function test_if_can_insert_multiple_records(): void
    {
        $values = [
            ['name' => 'User 1'],
            ['name' => 'User 2'],
        ];

        $this->repository
            ->shouldReceive('insert')
            ->once()
            ->with($values)
            ->andReturnTrue();

        $result = $this->service->insert($values);

        $this->assertTrue($result);
    }

    /**
     * Test if can upsert records.
     *
     * @return void
     */
    public function test_if_can_upsert_records(): void
    {
        $update = ['name'];
        $uniqueBy = ['email'];
        $values = [
            [
                'email' => 'test@test.com',
                'name' => 'Updated',
            ],
        ];

        $this->repository
            ->shouldReceive('upsert')
            ->once()
            ->with($values, $uniqueBy, $update);

        $this->service->upsert($values, $uniqueBy, $update);
    }

    /**
     * Test if can delete a record.
     *
     * @return void
     */
    public function test_if_can_delete_a_record(): void
    {
        $id = 1;

        $this->repository
            ->shouldReceive('delete')
            ->once()
            ->with($id);

        $this->service->delete($id);
    }

    /**
     * Test if can first or create a record.
     *
     * @return void
     */
    public function test_if_can_first_or_create_a_record(): void
    {
        $values = [
            'name' => 'User',
        ];
        $attributes = [
            'email' => 'test@test.com',
        ];

        $model = Mockery::mock(Model::class);

        $this->repository
            ->shouldReceive('firstOrCreate')
            ->once()
            ->with($attributes, $values)
            ->andReturn($model);

        $result = $this->service->firstOrCreate($attributes, $values);

        $this->assertSame($model, $result);
    }

    /**
     * Test if can update or create a record.
     *
     * @return void
     */
    public function test_if_can_update_or_create_a_record(): void
    {
        $values = [
            'name' => 'Updated User',
        ];
        $attributes = [
            'email' => 'test@test.com',
        ];

        $model = Mockery::mock(Model::class);

        $this->repository
            ->shouldReceive('updateOrCreate')
            ->once()
            ->with($attributes, $values)
            ->andReturn($model);

        $result = $this->service->updateOrCreate($attributes, $values);

        $this->assertSame($model, $result);
    }

    /**
     * Test if can find a record.
     *
     * @return void
     */
    public function test_if_can_find_a_record(): void
    {
        $id = 1;

        $model = Mockery::mock(Model::class);

        $this->repository
            ->shouldReceive('find')
            ->once()
            ->with($id)
            ->andReturn($model);

        $result = $this->service->find($id);

        $this->assertSame($model, $result);
    }

    /**
     * Test if can find a record by specific field.
     *
     * @return void
     */
    public function test_if_can_find_a_record_by_specific_field(): void
    {
        $field = 'email';
        $value = 'test@test.com';

        $model = Mockery::mock(Model::class);

        $this->repository
            ->shouldReceive('findBy')
            ->once()
            ->with($field, $value)
            ->andReturn($model);

        $result = $this->service->findBy($field, $value);

        $this->assertSame($model, $result);
    }

    /**
     * Test if can find all records by specific field.
     *
     * @return void
     */
    public function test_if_can_find_all_records_by_specific_field(): void
    {
        $field = 'status';
        $value = 'active';

        $collection = Mockery::mock(Collection::class);

        $this->repository
            ->shouldReceive('findAllBy')
            ->once()
            ->with($field, $value)
            ->andReturn($collection);

        $result = $this->service->findAllBy($field, $value);

        $this->assertSame($collection, $result);
    }

    /**
     * Test if can find records in a list of ids.
     *
     * @return void
     */
    public function test_if_can_find_records_in_a_list_of_ids(): void
    {
        $field = 'id';
        $ids = [1, 2, 3];

        $collection = Mockery::mock(Collection::class);

        $this->repository
            ->shouldReceive('findIn')
            ->once()
            ->with($field, $ids)
            ->andReturn($collection);

        $result = $this->service->findIn($field, $ids);

        $this->assertSame($collection, $result);
    }

    /**
     * Test if can find or fail a record.
     *
     * @return void
     */
    public function test_if_can_find_or_fail_a_record(): void
    {
        $id = 1;

        $model = Mockery::mock(Model::class);

        $this->repository
            ->shouldReceive('findOrFail')
            ->once()
            ->with($id)
            ->andReturn($model);

        $result = $this->service->findOrFail($id);

        $this->assertSame($model, $result);
    }

    /**
     * Test if can find or fail a record by specific field.
     *
     * @return void
     */
    public function test_if_can_find_or_fail_a_record_by_specific_field(): void
    {
        $field = 'slug';
        $value = 'my-slug';

        $model = Mockery::mock(Model::class);

        $this->repository
            ->shouldReceive('findOrFailBy')
            ->once()
            ->with($field, $value)
            ->andReturn($model);

        $result = $this->service->findOrFailBy($field, $value);

        $this->assertSame($model, $result);
    }

    /**
     * Test if can bulk delete records.
     *
     * @return void
     */
    public function test_if_can_bulk_delete_records(): void
    {
        $ids = [1, 2, 3];

        $this->repository
            ->shouldReceive('bulkDelete')
            ->once()
            ->with($ids)
            ->andReturn(3);

        $result = $this->service->bulkDelete($ids);

        $this->assertEquals(3, $result);
    }
}
