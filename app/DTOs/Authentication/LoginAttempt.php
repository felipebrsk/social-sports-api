<?php

namespace App\DTOs\Authentication;

class LoginAttempt
{
    /**
     * Create a new instance.
     *
     * @param string $email
     * @param string $password
     * @return void
     */
    public function __construct(
        public readonly string $email,
        public readonly string $password,
    ) {
        //
    }

    /**
     * Extract data from array.
     *
     * @param array<string, string> $data
     * @return self
     */
    public static function fromRequest(array $data): self
    {
        return new self(
            $data['email'] ?? '',
            $data['password'] ?? '',
        );
    }
}
