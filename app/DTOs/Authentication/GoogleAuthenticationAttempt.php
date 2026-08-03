<?php

namespace App\DTOs\Authentication;

class GoogleAuthenticationAttempt
{
    /**
     * Create a new instance.
     *
     * @param string $idToken
     * @return void
     */
    public function __construct(
        public readonly string $idToken,
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
            $data['id_token'] ?? '',
        );
    }
}
