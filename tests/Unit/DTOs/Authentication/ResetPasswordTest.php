<?php

namespace Tests\Unit\DTOs\Authentication;

use Tests\TestCase;
use App\DTOs\Authentication\ResetPassword;

class ResetPasswordTest extends TestCase
{
    /**
     * Test if the DTO can be successfully instantiated via constructor.
     *
     * @return void
     */
    public function test_if_dto_can_be_instantiated_via_constructor(): void
    {
        $token = fake()->md5();
        $password = fake()->password();
        $email = fake()->unique()->safeEmail();

        $dto = new ResetPassword($email, $token, $password);

        $this->assertInstanceOf(ResetPassword::class, $dto);
        $this->assertSame($email, $dto->email);
        $this->assertSame($token, $dto->token);
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
            'token' => fake()->md5(),
            'password' => fake()->password(),
            'email' => fake()->unique()->safeEmail(),
        ];

        $dto = ResetPassword::fromRequest($data);

        $this->assertInstanceOf(ResetPassword::class, $dto);
        $this->assertSame($data['email'], $dto->email);
        $this->assertSame($data['token'], $dto->token);
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

        $dto = ResetPassword::fromRequest($data);

        $this->assertInstanceOf(ResetPassword::class, $dto);
        $this->assertSame('', $dto->email);
        $this->assertSame('', $dto->token);
        $this->assertSame('', $dto->password);
    }
}
