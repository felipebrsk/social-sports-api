<?php

namespace Tests\Unit\DTOs\Authentication;

use Tests\TestCase;
use App\DTOs\Authentication\EmailVerify;

class EmailVerifyTest extends TestCase
{
    /**
     * Test if the DTO can be successfully instantiated via constructor.
     *
     * @return void
     */
    public function test_if_dto_can_be_instantiated_via_constructor(): void
    {
        $id = '123';
        $hash = fake()->md5();

        $dto = new EmailVerify($id, $hash);

        $this->assertInstanceOf(EmailVerify::class, $dto);
        $this->assertSame($id, $dto->id);
        $this->assertSame($hash, $dto->hash);
    }

    /**
     * Test if the DTO can be correctly constructed from a valid request array.
     *
     * @return void
     */
    public function test_if_dto_can_be_correctly_created_from_request_array(): void
    {
        $data = [
            'id' => '123',
            'hash' => fake()->md5(),
        ];

        $dto = EmailVerify::fromRequest($data);

        $this->assertInstanceOf(EmailVerify::class, $dto);
        $this->assertSame($data['id'], $dto->id);
        $this->assertSame($data['hash'], $dto->hash);
    }

    /**
     * Test if the DTO applies empty string fallbacks when request keys are missing.
     *
     * @return void
     */
    public function test_if_dto_applies_empty_string_fallbacks_when_keys_are_missing(): void
    {
        $data = [];

        $dto = EmailVerify::fromRequest($data);

        $this->assertInstanceOf(EmailVerify::class, $dto);
        $this->assertSame('', $dto->id);
        $this->assertSame('', $dto->hash);
    }
}
