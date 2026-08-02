<?php

namespace App\Enums;

use App\Contracts\Enums\EnumWithLabelInterface;

enum MediaTypeEnum: int implements EnumWithLabelInterface
{
    case IMAGE_TYPE_ID = 1;
    case FILE_TYPE_ID = 2;
    case VIDEO_TYPE_ID = 3;
    case LINK_TYPE_ID = 4;

    /**
     * {@inheritDoc}
     */
    public function label(): string
    {
        return match ($this) {
            self::IMAGE_TYPE_ID => 'Imagem',
            self::FILE_TYPE_ID => 'Arquivo',
            self::VIDEO_TYPE_ID => 'Vídeo',
            self::LINK_TYPE_ID => 'Link',
        };
    }
}
