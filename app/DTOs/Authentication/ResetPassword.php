<?php

namespace App\DTOs\Authentication;

class ResetPassword
{
    /**
     * Create a new instance.
     *
     * @param string $email
     * @param string $token
     * @param string $password
     * @return void
     */
    public function __construct(
        public readonly string $email,
        public readonly string $token,
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
            $data['token'] ?? '',
            $data['password'] ?? '',
        );
    }
}
