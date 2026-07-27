<?php

namespace App\Exceptions\Authentication;

use App\Exceptions\UnauthorizedException;

class InvalidCredentialsException extends UnauthorizedException
{
    /**
     * {@inheritDoc}
     */
    protected $message = 'Não foi possível encontrar o usuário. Por favor, verifique os dados e tente novamente.';
}
