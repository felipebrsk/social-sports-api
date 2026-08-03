<?php

namespace App\DTOs\Authentication;

class GoogleAuthenticationResult
{
    /**
     * Create a new instance.
     *
     * @param string $message
     * @param string $token
     * @return void
     */
    public function __construct(
        public readonly string $message,
        public readonly string $token,
    ) {
        //
    }
}
