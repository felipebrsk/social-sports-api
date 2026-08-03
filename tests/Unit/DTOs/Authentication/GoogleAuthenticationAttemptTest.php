<?php

namespace Tests\Unit\DTOs\Authentication;

use Tests\TestCase;
use App\DTOs\Authentication\GoogleAuthenticationAttempt;

class GoogleAuthenticationAttemptTest extends TestCase
{
    /**
     * Test if the DTO can be successfully instantiated via constructor.
     *
     * @return void
     */
    public function test_if_dto_can_be_instantiated_via_constructor(): void
    {
        $idToken = fake()->unique()->safeEmail();

        $dto = new GoogleAuthenticationAttempt($idToken);

        $this->assertInstanceOf(GoogleAuthenticationAttempt::class, $dto);
        $this->assertSame($idToken, $dto->idToken);
    }

    /**
     * Test if the DTO can be correctly constructed from a valid request array.
     *
     * @return void
     */
    public function test_if_dto_can_be_correctly_created_from_request_array(): void
    {
        $data = [
            'id_token' => fake()->unique()->safeEmail(),
        ];

        $dto = GoogleAuthenticationAttempt::fromRequest($data);

        $this->assertInstanceOf(GoogleAuthenticationAttempt::class, $dto);
        $this->assertSame($data['id_token'], $dto->idToken);
    }

    /**
     * Test if the DTO applies empty string fallbacks when request keys are missing.
     *
     * @return void
     */
    public function test_if_dto_applies_empty_string_fallbacks_when_keys_are_missing(): void
    {
        $data = [];

        $dto = GoogleAuthenticationAttempt::fromRequest($data);

        $this->assertInstanceOf(GoogleAuthenticationAttempt::class, $dto);
        $this->assertSame('', $dto->idToken);
    }
}
