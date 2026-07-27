<?php

namespace App\Exceptions\Authentication;

use App\Exceptions\BadRequestException;

class InvalidEmailVerifyDataException extends BadRequestException
{
    /**
     * {@inheritDoc}
     */
    protected $message = 'Argumentos inválidos. Tente novamente mais tarde!';
}
