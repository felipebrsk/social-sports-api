<?php

namespace Tests\Contracts\Models;

interface ShouldTestFillables
{
    /**
     * The contract fillable attributes that should be tested.
     */
    public function test_fillable_attributes(): void;
}
