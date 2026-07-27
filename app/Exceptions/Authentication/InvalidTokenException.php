<?php

namespace App\Exceptions\Authentication;

use App\Exceptions\BadRequestException;

class InvalidTokenException extends BadRequestException
{
    /**
     * {@inheritDoc}
     */
    protected $message = 'Não conseguimos validar a sua solicitação de redefinição de senha. Por favor, tente novamente.';
}
