<?php

namespace App\Exceptions;

use Symfony\Component\HttpFoundation\Response;

class ConflictException extends StatusCodeException
{
    /**
     * The response code.
     *
     * @var int
     */
    protected $statusCode = Response::HTTP_CONFLICT;
}
