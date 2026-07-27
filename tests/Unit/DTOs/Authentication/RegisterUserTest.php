<?php

namespace Tests\Unit\DTOs\Authentication;

use Tests\TestCase;
use App\DTOs\Authentication\RegisterUser;

class RegisterUserTest extends TestCase
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
        $name = fake()->name();

        $dto = new RegisterUser($name, $email, $password);

        $this->assertInstanceOf(RegisterUser::class, $dto);
        $this->assertSame($name, $dto->name);
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
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'password' => fake()->password(),
        ];

        $dto = RegisterUser::fromRequest($data);

        $this->assertInstanceOf(RegisterUser::class, $dto);
        $this->assertSame($data['name'], $dto->name);
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

        $dto = RegisterUser::fromRequest($data);

        $this->assertInstanceOf(RegisterUser::class, $dto);
        $this->assertSame('', $dto->name);
        $this->assertSame('', $dto->email);
        $this->assertSame('', $dto->password);
    }
}
