<?php

namespace App\Exceptions;

use Symfony\Component\HttpFoundation\Response;

class BadRequestException extends StatusCodeException
{
    /**
     * {@inheritDoc}
     */
    protected $statusCode = Response::HTTP_BAD_REQUEST;
}
