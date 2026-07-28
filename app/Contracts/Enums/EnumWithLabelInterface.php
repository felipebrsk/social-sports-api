<?php

namespace App\Contracts\Enums;

interface EnumWithLabelInterface
{
    /**
     * Get a friendly label for the enum value.
     *
     * @return string
     */
    public function label(): string;
}
