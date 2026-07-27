<?php

namespace App\Http\Controllers\Authentication;

use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\DTOs\Authentication\RegisterUser;
use App\Http\Requests\Authentication\RegisterRequest;
use App\Contracts\Services\ResponseServiceInterface;
use App\Contracts\Services\Authentication\RegisterServiceInterface;
use Symfony\Component\HttpFoundation\Response as HttpFoundationResponse;
use Dedoc\Scramble\Attributes\{
    Group,
    Endpoint,
    Response,
};

#[Group('Authentication')]
class RegisterController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        private readonly ResponseServiceInterface $response,
        private readonly RegisterServiceInterface $registerService,
    ) {
        //
    }

    /**
     * Register.
     *
     * Register user and create authentication token.
     *
     * @param RegisterRequest $request
     * @return JsonResponse
     */
    #[Endpoint(
        title: 'Register',
        description: 'Register user and create authentication token',
    )]
    #[Response(
        type: 'array{
            message: string,
            token: string
        }',
        examples: [[
            'message' => 'Usuário cadastrado com sucesso!',
            'token' => 'ey...',
        ]],
    )]
    public function __invoke(RegisterRequest $request): JsonResponse
    {
        /** @var array<string, string> $data */
        $data = $request->validated();

        $data = RegisterUser::fromRequest($data);

        $result = $this->registerService->register($data);

        return $this->response->setContent([
            'message' => $result->message,
            'token' => $result->token,
        ])->toJson(
            HttpFoundationResponse::HTTP_CREATED,
        );
    }
}
