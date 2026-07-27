<?php

namespace Tests\Unit\DTOs\Authentication;

use Tests\TestCase;
use App\DTOs\Authentication\RegisterResult;

class RegisterResultTest extends TestCase
{
    /**
     * Test if the DTO can be successfully instantiated via constructor.
     *
     * @return void
     */
    public function test_if_dto_can_be_instantiated_via_constructor(): void
    {
        $token = fake()->sentence();
        $message = fake()->realText();

        $dto = new RegisterResult($message, $token);

        $this->assertInstanceOf(RegisterResult::class, $dto);
        $this->assertSame($token, $dto->token);
        $this->assertSame($message, $dto->message);
    }
}
