<?php

namespace Tests\Unit\DTOs\Authentication;

use Tests\TestCase;
use App\DTOs\Authentication\LoginResult;

class LoginResultTest extends TestCase
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

        $dto = new LoginResult($message, $token);

        $this->assertInstanceOf(LoginResult::class, $dto);
        $this->assertSame($message, $dto->message);
    }
}
