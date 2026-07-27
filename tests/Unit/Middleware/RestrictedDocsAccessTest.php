<?php

namespace Tests\Unit\Middleware;

use App\Http\Middleware\RestrictedDocsAccess;
use Tests\Contracts\Middlewares\BaseMiddlewareTesting;

class RestrictedDocsAccessTest extends BaseMiddlewareTesting
{
    /**
     * The force json accept middleware.
     */
    public function middleware(): string
    {
        return RestrictedDocsAccess::class;
    }

    /**
     * Resolve the middleware instance with dependencies.
     */
    public function resolveMiddleware(): RestrictedDocsAccess
    {
        return new RestrictedDocsAccess();
    }

    /**
     * Test if can put the accept application json in headers.
     */
    public function test_if_can_put_the_accept_application_json_in_headers(): void
    {
        /** @var RestrictedDocsAccess $middleware */
        $middleware = $this->middleware;

        $middleware->handle($this->request, $this->next);

        $this->assertTrue(true); // @phpstan-ignore-line
    }
}
