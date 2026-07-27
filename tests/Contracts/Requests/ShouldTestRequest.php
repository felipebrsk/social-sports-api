<?php

namespace Tests\Contracts\Requests;

interface ShouldTestRequest
{
    /**
     * The contract to get request class-string.
     *
     * @return class-string
     */
    public function request(): string;
}
