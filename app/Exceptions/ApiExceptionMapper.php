<?php

namespace App\Exceptions;

use Throwable;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Symfony\Component\HttpKernel\Exception\{
    NotFoundHttpException,
    HttpExceptionInterface,
    MethodNotAllowedHttpException,
};

use function sprintf;
use function is_string;
use function get_class;

class ApiExceptionMapper
{
    /**
     * Map an exception to an array with the response content and status code.
     *
     * @return array{content: array{message: string, error_code?: string, errors?: array<string, array<int, string>>, debug?: mixed}, statusCode: int}
     */
    public static function map(Throwable $e): array
    {
        /** @var array<string, array<int, string>> $validationErrors */
        $validationErrors = $e instanceof ValidationException ? $e->errors() : [];

        $rawAllowHeader = $e instanceof MethodNotAllowedHttpException ? ($e->getHeaders()['Allow'] ?? '') : '';
        $allowHeader = is_string($rawAllowHeader) ? $rawAllowHeader : '';

        return match (true) {
            $e instanceof ValidationException => [
                'content' => [
                    'message' => 'Os dados fornecidos são inválidos.',
                    'error_code' => 'E422_VALIDATION_FAILED',
                    'errors' => $validationErrors,
                ],
                'statusCode' => Response::HTTP_UNPROCESSABLE_ENTITY,
            ],

            $e instanceof AuthenticationException => [
                'content' => [
                    'message' => $e->getMessage(),
                    'error_code' => 'E401_UNAUTHENTICATED',
                ],
                'statusCode' => Response::HTTP_UNAUTHORIZED,
            ],

            $e instanceof AuthorizationException => [
                'content' => [
                    'message' => 'Não autorizado.',
                    'error_code' => 'E403_FORBIDDEN',
                ],
                'statusCode' => Response::HTTP_FORBIDDEN,
            ],

            $e instanceof ModelNotFoundException, $e instanceof NotFoundHttpException => [
                'content' => [
                    'message' => 'Recurso ou rota não encontrados.',
                    'error_code' => 'E404_NOT_FOUND',
                ],
                'statusCode' => Response::HTTP_NOT_FOUND,
            ],

            $e instanceof MethodNotAllowedHttpException => [
                'content' => [
                    'message' => sprintf(
                        'O método %s não é suportado para esta rota. Métodos suportados: %s.',
                        request()->getMethod(),
                        $allowHeader,
                    ),
                    'error_code' => 'E405_METHOD_NOT_ALLOWED',
                ],
                'statusCode' => Response::HTTP_METHOD_NOT_ALLOWED,
            ],

            $e instanceof ThrottleRequestsException => [
                'content' => [
                    'message' => 'Muitas tentativas. Por favor, tente novamente mais tarde.',
                    'error_code' => 'E429_TOO_MANY_REQUESTS',
                ],
                'statusCode' => Response::HTTP_TOO_MANY_REQUESTS,
            ],

            $e instanceof StatusCodeException => [
                'content' => ['message' => $e->getMessage()],
                'statusCode' => $e->getStatusCode(),
            ],

            $e instanceof HttpExceptionInterface => [
                'content' => ['message' => $e->getMessage()],
                'statusCode' => $e->getStatusCode(),
            ],

            default => [
                'content' => [
                    'message' => 'Ocorreu um erro interno no servidor.',
                    'error_code' => 'E500_INTERNAL_SERVER_ERROR',
                    'debug' => app()->hasDebugModeEnabled() ? [
                        'exception' => get_class($e),
                        'message' => $e->getMessage(),
                        'file' => $e->getFile(),
                        'line' => $e->getLine(),
                    ] : null,
                ],
                'statusCode' => Response::HTTP_INTERNAL_SERVER_ERROR,
            ],
        };
    }
}
