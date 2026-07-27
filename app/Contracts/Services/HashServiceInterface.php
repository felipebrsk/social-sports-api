<?php

namespace App\Contracts\Services;

interface HashServiceInterface
{
    /**
     * Checks if a plain text matches a given hash.
     *
     * @param string $plainText
     * @param string $hashedText
     * @return bool
     */
    public function check(string $plainText, string $hashedText): bool;
}
