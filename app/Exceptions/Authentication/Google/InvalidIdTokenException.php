<?php

namespace App\Exceptions\Authentication\Google;

use App\Exceptions\UnprocessableEntityException;

class InvalidIdTokenException extends UnprocessableEntityException
{
    /**
     * {@inheritDoc}
     */
    protected $message = 'Token do Google inválido ou expirado.';
}
