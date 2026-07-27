<?php

namespace App\DTOs\Authentication;

class LoginResult
{
    /**
     * Create a new instance.
     *
     * @return void
     */
    public function __construct(
        public readonly string $message,
        public readonly string $token,
    ) {
        //
    }
}
