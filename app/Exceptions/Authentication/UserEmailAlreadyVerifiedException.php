<?php

namespace App\Exceptions\Authentication;

use App\Exceptions\ConflictException;

class UserEmailAlreadyVerifiedException extends ConflictException
{
    /**
     * {@inheritDoc}
     */
    protected $message = 'O e-mail do usuário já foi verificado, nenhuma ação é necessária!';
}
