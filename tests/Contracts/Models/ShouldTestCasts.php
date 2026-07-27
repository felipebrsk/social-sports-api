<?php

namespace Tests\Contracts\Models;

interface ShouldTestCasts
{
    /**
     * The contract casts attributes that should be tested.
     */
    public function test_casts_attributes(): void;
}
