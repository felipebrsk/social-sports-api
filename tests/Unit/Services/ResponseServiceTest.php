<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\ResponseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;

class ResponseServiceTest extends TestCase
{
    /**
     * Test if the constructor sets the message correctly.
     *
     * @return void
     */
    public function test_if_constructor_sets_message(): void
    {
        $service = new ResponseService('Initial message');

        $this->assertEquals('Initial message', $service->message);
    }

    /**
     * Test if setters work correctly and provide a fluent interface.
     *
     * @return void
     */
    public function test_if_setters_are_fluent_and_set_properties(): void
    {
        $service = new ResponseService();

        $result = $service
            ->setMessage('Updated message')
            ->setContent(['key' => 'value']);

        $this->assertSame($service, $result);
        $this->assertEquals('Updated message', $service->message);
        $this->assertEquals(['key' => 'value'], $service->content);
    }

    /**
     * Test if toMessage returns the correct array structure.
     *
     * @return void
     */
    public function test_if_to_message_returns_correct_array(): void
    {
        $service = new ResponseService('Success');

        $expected = [
            'message' => 'Success',
        ];

        $this->assertEquals($expected, $service->toMessage());
    }

    /**
     * Test if toArray returns the correct array structure with primitive data.
     *
     * @return void
     */
    public function test_if_to_array_returns_correct_data_structure(): void
    {
        $service = new ResponseService();
        $service->setContent('plain_string_data');

        $expected = [
            'data' => 'plain_string_data',
        ];

        $this->assertEquals($expected, $service->toArray());
    }

    /**
     * Test if toArray properly converts a Laravel Collection to an array.
     *
     * @return void
     */
    public function test_if_to_array_converts_collection_properly(): void
    {
        $collection = new Collection(['item 1', 'item 2']);

        $service = new ResponseService();
        $service->setContent($collection);

        $expected = [
            'data' => [
                'item 1',
                'item 2',
            ],
        ];

        $this->assertEquals($expected, $service->toArray());
    }

    /**
     * Test if toJson returns a JsonResponse with message format when message is present.
     *
     * @return void
     */
    public function test_if_to_json_prioritizes_message_format(): void
    {
        $service = new ResponseService('Action completed');
        $service->setContent(['hidden' => 'data']);

        $response = $service->toJson(201);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(201, $response->getStatusCode());

        $expectedJson = json_encode(['message' => 'Action completed']);
        $this->assertJsonStringEqualsJsonString($expectedJson, $response->getContent()); // @phpstan-ignore-line
    }

    /**
     * Test if toJson returns a JsonResponse with data format when message is empty.
     *
     * @return void
     */
    public function test_if_to_json_returns_data_format_when_message_is_empty(): void
    {
        $service = new ResponseService();
        $service->setContent(['id' => 1, 'name' => 'John']);

        $response = $service->toJson(200);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());

        $expectedJson = json_encode(['data' => ['id' => 1, 'name' => 'John']]);
        $this->assertJsonStringEqualsJsonString($expectedJson, $response->getContent()); // @phpstan-ignore-line
    }
}
