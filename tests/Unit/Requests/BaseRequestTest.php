<?php

namespace Tests\Unit\Requests;

use Tests\TestCase;
use ReflectionMethod;
use App\Http\Requests\BaseRequest;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * @covers \App\Http\Requests\BaseRequest
 */
class BaseRequestTest extends TestCase
{
    /**
     * Test if the authorize method always returns true.
     *
     * @return void
     */
    public function test_if_authorize_always_returns_true(): void
    {
        $request = new ConcreteBaseRequestStub();

        $this->assertTrue($request->authorize());
    }

    /**
     * Test if it does not modify the request data when booleans array is empty.
     *
     * @return void
     */
    public function test_it_does_not_modify_data_when_booleans_array_is_empty(): void
    {
        $inputData = ['active' => 'true', 'name' => 'Test'];

        $request = ConcreteBaseRequestStub::create('/test', 'POST', $inputData);
        $request->setBooleans([]);

        $this->invokePrepareValidation($request);

        $this->assertEquals($inputData, $request->all());
        $this->assertSame('true', $request->input('active')); // Deve continuar como string
    }

    /**
     * Test if the request correctly casts specified boolean fields based on patterns.
     *
     * @param array<int, string> $booleans
     * @param array<string, mixed> $input
     * @param array<string, mixed> $expected
     * @return void
     */
    #[DataProvider('booleanCastingProvider')]
    public function test_it_casts_boolean_fields_correctly(array $booleans, array $input, array $expected): void
    {
        $request = ConcreteBaseRequestStub::create('/test', 'POST', $input);
        $request->setBooleans($booleans);

        $this->invokePrepareValidation($request);

        $this->assertEquals($expected, $request->all());
    }

    /**
     * Data provider for testing the boolean casting logic.
     *
     * @return array<string, array{booleans: array<int, string>, input: array<string, mixed>, expected: array<string, mixed>}>
     */
    public static function booleanCastingProvider(): array
    {
        return [
            'simples casting de strings textuais (true/false)' => [
                'booleans' => ['active', 'is_admin'],
                'input' => ['active' => 'true', 'is_admin' => 'false', 'name' => 'John'],
                'expected' => ['active' => true, 'is_admin' => false, 'name' => 'John'],
            ],
            'simples casting de valores numéricos (1/0)' => [
                'booleans' => ['active', 'is_admin'],
                'input' => ['active' => '1', 'is_admin' => '0'],
                'expected' => ['active' => true, 'is_admin' => false],
            ],
            'simples casting de booleanos literais (já enviados como bool)' => [
                'booleans' => ['active'],
                'input' => ['active' => true],
                'expected' => ['active' => true],
            ],
            'casting de propriedades aninhadas (dot notation literal)' => [
                'booleans' => ['settings.dark_mode'],
                'input' => ['settings' => ['dark_mode' => 'true', 'notifications' => '1']],
                'expected' => ['settings' => ['dark_mode' => true, 'notifications' => '1']],
            ],
            'casting aninhado com wildcard (array de objetos)' => [
                'booleans' => ['stores.*.active'],
                'input' => [
                    'stores' => [
                        ['id' => 1, 'active' => 'true'],
                        ['id' => 2, 'active' => 'false'],
                        ['id' => 3, 'active' => '1'],
                    ],
                ],
                'expected' => [
                    'stores' => [
                        ['id' => 1, 'active' => true],
                        ['id' => 2, 'active' => false],
                        ['id' => 3, 'active' => true],
                    ],
                ],
            ],
            'falha no casting converte para null (comportamento do FILTER_NULL_ON_FAILURE)' => [
                'booleans' => ['active'],
                'input' => ['active' => 'not-a-boolean-value'],
                'expected' => ['active' => 'not-a-boolean-value'],
            ],
            'apenas campos mapeados devem sofrer alteração' => [
                'booleans' => ['active'],
                'input' => ['active' => 'true', 'ignored_field' => 'true'],
                'expected' => ['active' => true, 'ignored_field' => 'true'],
            ],
        ];
    }

    /**
     * Invoke the protected `prepareForValidation` method on the request object.
     *
     * @param BaseRequest $request
     * @return void
     */
    private function invokePrepareValidation(BaseRequest $request): void
    {
        $method = new ReflectionMethod(BaseRequest::class, 'prepareForValidation');

        $method->invoke($request);
    }
}

/**
 * Concrete implementation of the abstract BaseRequest for testing purposes.
 */
class ConcreteBaseRequestStub extends BaseRequest
{
    /**
     * Set the boolean fields to test the wildcard combinations.
     *
     * @param array<int, string> $booleans
     * @return void
     */
    public function setBooleans(array $booleans): void
    {
        $this->booleans = $booleans;
    }

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
