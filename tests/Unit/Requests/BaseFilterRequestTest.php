<?php

namespace Tests\Unit\Requests;

use Tests\TestCase;
use ReflectionMethod;
use App\Http\Requests\BaseFilterRequest;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * @covers BaseFilterRequest
 */
class BaseFilterRequestTest extends TestCase
{
    /**
     * Test that filters are merged into the request when `filter_by` is an array.
     *
     * @return void
     */
    public function test_if_it_merges_filters_when_filter_by_is_an_array(): void
    {
        $filters = ['status' => 'active', 'name' => 'Test User'];

        $request = ConcreteFilterRequestStub::create('/test', 'GET', ['filter_by' => $filters]);

        $this->invokePrepareValidation($request);

        $this->assertEquals('active', $request->input('status'));
        $this->assertEquals('Test User', $request->input('name'));
        $this->assertEquals($filters, $request->input('filter_by'));
    }

    /**
     * Test that direct query parameters are correctly handled as filters.
     *
     * @return void
     */
    public function test_if_it_handles_direct_query_parameters_as_filters(): void
    {
        $directFilters = ['status' => 'pending', 'name' => 'Another User'];

        $request = ConcreteFilterRequestStub::create('/test', 'GET', $directFilters);

        $this->invokePrepareValidation($request);

        // Check that direct filters were merged to the top level
        $this->assertEquals('pending', $request->input('status'));
        $this->assertEquals('Another User', $request->input('name'));

        // Check that 'filter_by' was created and contains all the initial parameters
        $this->assertEquals($directFilters, $request->input('filter_by'));
    }

    /**
     * Test that filters are not merged when `filter_by` is not an array.
     *
     * @return void
     */
    public function test_it_does_not_merge_filters_when_filter_by_is_not_an_array(): void
    {
        $request = ConcreteFilterRequestStub::create('/test', 'GET', ['filter_by' => 'invalid-string']);

        $this->invokePrepareValidation($request);

        $this->assertNull($request->input('status'));
        $this->assertEquals('invalid-string', $request->input('filter_by'));
        $this->assertCount(1, $request->all());
    }

    /**
     * Test that the request handles the absence of any parameters gracefully.
     *
     * @return void
     */
    public function test_it_handles_empty_request_gracefully(): void
    {
        $request = ConcreteFilterRequestStub::create('/test', 'GET');

        $this->invokePrepareValidation($request);

        // When no parameters are sent, 'filter_by' should be an empty array.
        $this->assertEquals([], $request->input('filter_by'));
        $this->assertCount(1, $request->all());
    }

    /**
     * Test that the validated method returns a specific key using dot notation when requested.
     *
     * @return void
     */
    public function test_validated_method_returns_specific_key_value(): void
    {
        $request = new class () extends BaseFilterRequest {
            /**
             * Get the class rules.
             *
             * @return array{sort_by: string, status: string}
             */
            public function rules(): array
            {
                return ['sort_by' => 'sometimes', 'status' => 'sometimes'];
            }
        };

        $flatData = ['sort_by' => 'name', 'status' => 'active'];

        $validator = $this->app['validator']->make($flatData, $request->rules());

        $request->setValidator($validator);

        $this->assertEquals('name', $request->validated('sort_by'));
    }

    /**
     * Test that the validated method returns the default value if the requested key does not exist.
     *
     * @return void
     */
    public function test_validated_method_returns_default_value_when_key_does_not_exist(): void
    {
        $request = new class () extends BaseFilterRequest {
            /**
             * Get the class rules.
             *
             * @return array{sort_by: string}
             */
            public function rules(): array
            {
                return ['sort_by' => 'sometimes'];
            }
        };

        $flatData = ['sort_by' => 'name'];

        $validator = $this->app['validator']->make($flatData, $request->rules());

        $request->setValidator($validator);

        $this->assertEquals('desc', $request->validated('sort_order', 'desc'));
    }

    /**
     * Test that the validated method correctly restructures flat data into the expected format.
     *
     * @param array<string, mixed> $flatValidatedData The flat data that `parent::validated()` would return.
     * @param array<string, mixed> $expectedStructuredData The expected nested output from our `validated()` method.
     * @return void
     */
    #[DataProvider('validatedRestructuringProvider')]
    public function test_validated_method_restructures_data_correctly(array $flatValidatedData, array $expectedStructuredData): void
    {
        $request = new class () extends BaseFilterRequest {
            /**
             * The test rules.
             *
             * @var array<mixed>
             */
            public array $testRules = [];

            /**
             * {@inheritDoc}
             *
             * @return array<mixed>
             */
            public function rules(): array
            {
                return $this->testRules;
            }
        };

        $request->testRules = collect($flatValidatedData)->map(fn () => 'sometimes')->toArray();

        $validator = $this->app['validator']->make($flatValidatedData, $request->rules());

        $request->setValidator($validator);

        $this->assertEquals($expectedStructuredData, $request->validated());
    }

    /**
     * Data provider for testing the `validated` method's restructuring logic.
     *
     * @return array<string, array<string, array<string, array<string, bool|int|string>|bool|int|string>>>
     */
    public static function validatedRestructuringProvider(): array
    {
        return [
            'com dados mistos de filtro e ordenação' => [
                'flatValidatedData' => [
                    'unread' => true,
                    'sort_by' => 'title',
                    'sector_id' => 5,
                    'per_page' => 50,
                    'limit' => 100,
                ],
                'expectedStructuredData' => [
                    'sort_by' => 'title',
                    'per_page' => 50,
                    'limit' => 100,
                    'filter_by' => [
                        'unread' => true,
                        'sector_id' => 5,
                    ],
                ],
            ],
            'apenas com dados de filtro' => [
                'flatValidatedData' => [
                    'status' => 'active',
                    'title' => 'test',
                ],
                'expectedStructuredData' => [
                    'filter_by' => [
                        'status' => 'active',
                        'title' => 'test',
                    ],
                ],
            ],
            'apenas com dados de ordenação/paginação' => [
                'flatValidatedData' => [
                    'limit' => 100,
                    'per_page' => 100,
                    'sort_by' => 'title',
                    'sort_order' => 'desc',
                ],
                'expectedStructuredData' => [
                    'limit' => 100,
                    'per_page' => 100,
                    'sort_by' => 'title',
                    'sort_order' => 'desc',
                ],
            ],
            'com dados vazios' => [
                'flatValidatedData' => [],
                'expectedStructuredData' => [],
            ],
        ];
    }

    /**
     * Invoke the protected `prepareForValidation` method on the request object.
     *
     * @param BaseFilterRequest $request
     * @return void
     */
    private function invokePrepareValidation(BaseFilterRequest $request): void
    {
        $method = new ReflectionMethod(BaseFilterRequest::class, 'prepareForValidation');

        $method->invoke($request);
    }
}

/**
 * Concrete implementation of the abstract BaseFilterRequest for testing purposes.
 */
class ConcreteFilterRequestStub extends BaseFilterRequest
{
    /**
     * The concrete rules.
     *
     * @return array<mixed>
     */
    public function rules(): array
    {
        return [];
    }
}
