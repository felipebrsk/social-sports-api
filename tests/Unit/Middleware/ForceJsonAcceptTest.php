<?php

namespace Tests\Unit\Middleware;

use App\Http\Middleware\ForceJsonAccept;
use Tests\Contracts\Middlewares\BaseMiddlewareTesting;

class ForceJsonAcceptTest extends BaseMiddlewareTesting
{
    /**
     * The force json accept middleware.
     */
    public function middleware(): string
    {
        return ForceJsonAccept::class;
    }

    /**
     * Resolve the middleware instance with dependencies.
     */
    public function resolveMiddleware(): ForceJsonAccept
    {
        return new ForceJsonAccept();
    }

    /**
     * Test if can put the accept application json in headers.
     */
    public function test_if_can_put_the_accept_application_json_in_headers(): void
    {
        /** @var ForceJsonAccept $middleware */
        $middleware = $this->middleware;

        $middleware->handle($this->request, $this->next);

        $this->assertEquals('application/json', $this->request->header('Accept'));
    }
}
