<?php

namespace Tests\Contracts\Middlewares;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Tests\TestCase;

abstract class BaseMiddlewareTesting extends TestCase implements ShouldTestMiddlewares
{
    /**
     * The middleware.
     */
    protected object $middleware;

    /**
     * The dummy request.
     */
    protected Request $request;

    /**
     * The closure next.
     */
    protected Closure $next;

    /**
     * Setup new test environments.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->middleware = $this->resolveMiddleware();

        $this->request = Request::create('/', 'GET');

        $this->next = function () {
            return new Response('Next middleware');
        };
    }
}
