<?php

namespace App\Enums;

use App\Contracts\Enums\EnumWithLabelInterface;

enum ProviderEnum: int implements EnumWithLabelInterface
{
    case GOOGLE_ID = 1;

    /**
     * {@inheritDoc}
     */
    public function label(): string
    {
        return match ($this) {
            self::GOOGLE_ID => 'Google',
        };
    }
}
