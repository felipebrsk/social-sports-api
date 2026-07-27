<?php

namespace Tests\Contracts\Resources;

interface ShouldTestResource
{
    /**
     * The contract to get resource class-string.
     *
     * @return class-string
     */
    public function resource(): string;

    /**
     * The testable instance.
     */
    public function instanceable(): mixed;
}
