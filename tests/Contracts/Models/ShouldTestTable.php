<?php

namespace Tests\Contracts\Models;

interface ShouldTestTable
{
    /**
     * The table attribute that should be tested.
     */
    public function test_table_attribute(): void;
}
