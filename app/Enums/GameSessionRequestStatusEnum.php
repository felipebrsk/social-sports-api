<?php

namespace App\Enums;

use App\Contracts\Enums\EnumWithLabelInterface;

enum GameSessionRequestStatusEnum: int implements EnumWithLabelInterface
{
    case PENDING = 1;
    case APPROVED = 2;
    case REFUSED = 3;

    /**
     * {@inheritDoc}
     */
    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pendente',
            self::APPROVED => 'Aprovado',
            self::REFUSED => 'Recusado',
        };
    }
}
