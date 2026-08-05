<?php

namespace Tests\Unit\DTOs\GameSession;

use Tests\TestCase;
use App\DTOs\GameSession\GameSessionDetails;

class GameSessionDetailsTest extends TestCase
{
    /**
     * Test if the DTO can be successfully instantiated via constructor.
     *
     * @return void
     */
    public function test_if_dto_can_be_instantiated_via_constructor(): void
    {
        $id = 123;
        $latitude = fake()->latitude();
        $longitude = fake()->longitude();

        $dto = new GameSessionDetails($id, $latitude, $longitude);

        $this->assertInstanceOf(GameSessionDetails::class, $dto);
        $this->assertSame($id, $dto->id);
        $this->assertSame($latitude, $dto->latitude);
        $this->assertSame($longitude, $dto->longitude);
    }

    /**
     * Test if the DTO can be correctly constructed from a valid request array.
     *
     * @return void
     */
    public function test_if_dto_can_be_correctly_created_from_request_array(): void
    {
        $id = 123;

        $data = [
            'latitude' => fake()->latitude(),
            'longitude' => fake()->longitude(),
        ];

        /** @var array<string, string|null> $data */
        $dto = GameSessionDetails::fromRequest($id, $data);

        $this->assertInstanceOf(GameSessionDetails::class, $dto);
        $this->assertSame($id, $dto->id);
        $this->assertSame($data['latitude'], $dto->latitude);
        $this->assertSame($data['longitude'], $dto->longitude);
    }

    /**
     * Test if the DTO applies empty string fallbacks when request keys are missing.
     *
     * @return void
     */
    public function test_if_dto_applies_empty_string_fallbacks_when_keys_are_missing(): void
    {
        $data = [];

        $dto = GameSessionDetails::fromRequest(1, $data);

        $this->assertInstanceOf(GameSessionDetails::class, $dto);
        $this->assertSame(1, $dto->id);
        $this->assertNull($dto->latitude);
        $this->assertNull($dto->longitude);
    }
}
