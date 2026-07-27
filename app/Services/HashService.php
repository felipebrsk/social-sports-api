<?php

namespace App\Services;

use Illuminate\Contracts\Hashing\Hasher;
use App\Contracts\Services\HashServiceInterface;

class HashService implements HashServiceInterface
{
    /**
     * Create a new service instance.
     *
     * @return void
     */
    public function __construct(
        private readonly Hasher $hasher,
    ) {
        //
    }

    /**
     * {@inheritDoc}
     */
    public function check(string $plainText, string $hashedText): bool
    {
        return $this->hasher->check($plainText, $hashedText);
    }
}
