<?php

namespace Tests\Contracts\Models;

interface ShouldTestGuarded
{
    /**
     * The contract guarded attributes that should be tested.
     */
    public function test_guarded_attributes(): void;
}
