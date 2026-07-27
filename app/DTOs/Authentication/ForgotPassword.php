<?php

namespace App\DTOs\Authentication;

class ForgotPassword
{
    /**
     * Create a new instance.
     *
     * @param string $email
     * @return void
     */
    public function __construct(
        public readonly string $email,
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
        );
    }
}
