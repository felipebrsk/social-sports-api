<?php

namespace App\Http\Controllers\Authentication\Oauth;

use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Authentication\GoogleRequest;
use App\Contracts\Services\ResponseServiceInterface;
use App\DTOs\Authentication\GoogleAuthenticationAttempt;
use App\Contracts\Services\Authentication\GoogleServiceInterface;
use Dedoc\Scramble\Attributes\{
    Group,
    Endpoint,
    Response,
};

#[Group('Authentication: OAuth')]
class GoogleController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        private readonly ResponseServiceInterface $response,
        private readonly GoogleServiceInterface $googleService,
    ) {
        //
    }

    /**
     * Login with Google.
     *
     * Login user through Google and create authentication token.
     *
     * @param GoogleRequest $request
     * @return JsonResponse
     */
    #[Endpoint(
        title: 'Login with Google',
        description: 'Login user through Google and create authentication token',
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
    public function __invoke(GoogleRequest $request): JsonResponse
    {
        /** @var array<string, string> $data */
        $data = $request->validated();

        $data = GoogleAuthenticationAttempt::fromRequest($data);

        $result = $this->googleService->login($data);

        return $this->response->setContent([
            'message' => $result->message,
            'token' => $result->token,
        ])->toJson();
    }
}
