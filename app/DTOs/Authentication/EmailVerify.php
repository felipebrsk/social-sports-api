<?php

namespace App\DTOs\Authentication;

class EmailVerify
{
    /**
     * Create a new instance.
     *
     * @param string $id
     * @param string $hash
     * @return void
     */
    public function __construct(
        public readonly string $id,
        public readonly string $hash,
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
            $data['id'] ?? '',
            $data['hash'] ?? '',
        );
    }
}
