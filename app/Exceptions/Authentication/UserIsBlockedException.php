<?php

namespace App\Exceptions\Authentication;

use App\Exceptions\ForbiddenException;

class UserIsBlockedException extends ForbiddenException
{
    /**
     * {@inheritDoc}
     */
    protected $message = 'O acesso do seu usuário foi restrito na plataforma.';
}
