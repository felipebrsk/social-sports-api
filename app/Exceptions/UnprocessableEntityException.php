<?php

namespace App\Exceptions;

use Symfony\Component\HttpFoundation\Response;

class UnprocessableEntityException extends StatusCodeException
{
    /**
     * {@inheritDoc}
     */
    protected $statusCode = Response::HTTP_UNPROCESSABLE_ENTITY;
}
