<?php

namespace App\Exceptions;

use Symfony\Component\HttpFoundation\Response;

class UnauthorizedException extends StatusCodeException
{
    /**
     * {@inheritDoc}
     */
    protected $statusCode = Response::HTTP_UNAUTHORIZED;
}
