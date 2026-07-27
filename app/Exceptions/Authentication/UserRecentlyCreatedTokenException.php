<?php

namespace App\Exceptions\Authentication;

use App\Exceptions\BadRequestException;

class UserRecentlyCreatedTokenException extends BadRequestException
{
    /**
     * The response message.
     *
     * @var string
     */
    protected $message = 'Você deve esperar alguns segundos para solicitar a redefinição de senha novamente.';
}
