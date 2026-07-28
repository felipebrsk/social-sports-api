<?php

namespace App\Contracts\Enums;

interface EnumWithValuesInterface
{
    /**
     * Converts all enum cases to an array of values.
     *
     * @return array<int, mixed>
     */
    public static function values(): array;
}
