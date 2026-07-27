<?php

use Illuminate\Http\Request;
use App\Exceptions\ApiExceptionMapper;
use Illuminate\Foundation\Application;
use App\Exceptions\StatusCodeException;
use App\Http\Middleware\ForceJsonAccept;
use App\Console\Commands\BuilderCommand;
use App\Contracts\Services\ResponseServiceInterface;
use Symfony\Component\HttpFoundation\Request as FoundationRequest;
use Illuminate\Support\Facades\{
    Log,
    Route,
};
use Illuminate\Foundation\Configuration\{
    Middleware,
    Exceptions,
};

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        health: '/',
        apiPrefix: '',
        api: __DIR__ . '/../routes/api.php',
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        then: function () {
            Route::middleware(['api', 'auth', 'verified'])->group(base_path('routes/auth.php'));
        },
    )->withMiddleware(function (Middleware $middleware): void {
        $middleware->append([
            ForceJsonAccept::class,
        ])->trustProxies(
            headers: FoundationRequest::HEADER_X_FORWARDED_FOR |
                FoundationRequest::HEADER_X_FORWARDED_HOST |
                FoundationRequest::HEADER_X_FORWARDED_PORT |
                FoundationRequest::HEADER_X_FORWARDED_PROTO |
                FoundationRequest::HEADER_X_FORWARDED_AWS_ELB
        );
    })->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->dontReport([
            StatusCodeException::class,
        ])->dontFlash([
            'password',
            'current_password',
            'password_confirmation',
        ])->renderable(function (Throwable $e, Request $request) {
            if ($request->wantsJson()) {
                ['content' => $responseContent, 'statusCode' => $statusCode] = ApiExceptionMapper::map($e);

                /** @var ResponseServiceInterface $responseService */
                $responseService = resolve(ResponseServiceInterface::class);

                return $responseService
                    ->unwrap()
                    ->setContent($responseContent)
                    ->toJson($statusCode);
            }
        })->reportable(function (Throwable $e) {
            Log::error($e->getMessage(), [
                'exception' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);
        });
    })->withCommands([
        BuilderCommand::class,
    ])->create();
