<?php

namespace App\Http\Controllers\Authentication;

use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\DTOs\Authentication\LoginAttempt;
use App\Http\Requests\Authentication\LoginRequest;
use App\Contracts\Services\ResponseServiceInterface;
use App\Contracts\Services\Authentication\AuthenticationServiceInterface;
use Dedoc\Scramble\Attributes\{
    Group,
    Endpoint,
    Response,
};

#[Group('Authentication')]
class LoginController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        private readonly ResponseServiceInterface $response,
        private readonly AuthenticationServiceInterface $authenticationService,
    ) {
        //
    }

    /**
     * Login.
     *
     * Login user and create authentication token.
     *
     * @param LoginRequest $request
     * @return JsonResponse
     */
    #[Endpoint(
        title: 'Login',
        description: 'Login user and create authentication token',
    )]
    #[Response(
        type: 'array{
            message: string,
            token: string
        }',
        examples: [[
            'message' => 'Usuário autenticado com sucesso!',
            'token' => 'ey...',
        ]],
    )]
    public function __invoke(LoginRequest $request): JsonResponse
    {
        /** @var array<string, string> $data */
        $data = $request->validated();

        $data = LoginAttempt::fromRequest($data);

        $result = $this->authenticationService->login($data);

        return $this->response->setContent([
            'message' => $result->message,
            'token' => $result->token,
        ])->toJson();
    }
}
