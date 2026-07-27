<?php

namespace App\Exceptions;

use Symfony\Component\HttpFoundation\Response;

class ForbiddenException extends StatusCodeException
{
    /**
     * {@inheritDoc}
     */
    protected $statusCode = Response::HTTP_FORBIDDEN;
}
