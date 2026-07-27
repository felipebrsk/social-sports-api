<?php

namespace Tests\Unit\DTOs\Authentication;

use Tests\TestCase;
use App\DTOs\Authentication\LoginAttempt;

class LoginAttemptTest extends TestCase
{
    /**
     * Test if the DTO can be successfully instantiated via constructor.
     *
     * @return void
     */
    public function test_if_dto_can_be_instantiated_via_constructor(): void
    {
        $email = fake()->unique()->safeEmail();
        $password = fake()->password();

        $dto = new LoginAttempt($email, $password);

        $this->assertInstanceOf(LoginAttempt::class, $dto);
        $this->assertSame($email, $dto->email);
        $this->assertSame($password, $dto->password);
    }

    /**
     * Test if the DTO can be correctly constructed from a valid request array.
     *
     * @return void
     */
    public function test_if_dto_can_be_correctly_created_from_request_array(): void
    {
        $data = [
            'email' => fake()->unique()->safeEmail(),
            'password' => fake()->password(),
        ];

        $dto = LoginAttempt::fromRequest($data);

        $this->assertInstanceOf(LoginAttempt::class, $dto);
        $this->assertSame($data['email'], $dto->email);
        $this->assertSame($data['password'], $dto->password);
    }

    /**
     * Test if the DTO applies empty string fallbacks when request keys are missing.
     *
     * @return void
     */
    public function test_if_dto_applies_empty_string_fallbacks_when_keys_are_missing(): void
    {
        $data = [];

        $dto = LoginAttempt::fromRequest($data);

        $this->assertInstanceOf(LoginAttempt::class, $dto);
        $this->assertSame('', $dto->email);
        $this->assertSame('', $dto->password);
    }
}
