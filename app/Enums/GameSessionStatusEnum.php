<?php

namespace App\Enums;

use App\Contracts\Enums\EnumWithLabelInterface;

enum GameSessionStatusEnum: int implements EnumWithLabelInterface
{
    case OPEN = 1;
    case FULL = 2;
    case CANCELLED = 3;
    case FINISHED = 4;

    /**
     * {@inheritDoc}
     */
    public function label(): string
    {
        return match ($this) {
            self::OPEN => 'Aberto',
            self::FULL => 'Lotado',
            self::CANCELLED => 'Cancelado',
            self::FINISHED => 'Concluído/Finalizado',
        };
    }
}
