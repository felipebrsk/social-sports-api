<?php

namespace App\Exceptions\Authentication\Google;

use App\Exceptions\UnprocessableEntityException;

class UnverifiedGoogleEmailException extends UnprocessableEntityException
{
    /**
     * {@inheritDoc}
     */
    protected $message = 'O e-mail da conta Google não está verificado. Tente verificar o e-mail pelo Google ou acesse com o seu e-mail normalmente.';
}
