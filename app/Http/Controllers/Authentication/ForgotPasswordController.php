<?php

namespace App\Http\Controllers\Authentication;

use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\DTOs\Authentication\ForgotPassword;
use App\Contracts\Services\ResponseServiceInterface;
use App\Http\Requests\Authentication\ForgotPasswordRequest;
use App\Contracts\Services\Authentication\ResetPasswordServiceInterface;
use Dedoc\Scramble\Attributes\{
    Group,
    Response,
};

#[Group('Authentication')]
class ForgotPasswordController extends Controller
{
    /**
     * Create a new class instance.
     *
     * @param ResponseServiceInterface $response
     * @param ResetPasswordServiceInterface $resetPasswordService
     * @return void
     */
    public function __construct(
        private readonly ResponseServiceInterface $response,
        private readonly ResetPasswordServiceInterface $resetPasswordService,
    ) {
        //
    }

    /**
     * Request Password Reset Link
     *
     * Sends a secure, time-sensitive password reset link to the user's registered email address.
     *
     * @param ForgotPasswordRequest $request
     * @return JsonResponse
     */
    #[Response(
        type: 'array{
            message: string
        }',
        examples: [[
            'message' => 'Um link para a redefinição de senha será enviado para o seu e-mail!'
        ]],
    )]
    public function __invoke(ForgotPasswordRequest $request): JsonResponse
    {
        /** @var array<string, string> $data */
        $data = $request->validated();

        $dto = ForgotPassword::fromRequest($data);

        $this->resetPasswordService->sendResetNotification($dto);

        return $this->response
            ->setMessage('Um link para a redefinição de senha será enviado para o seu e-mail!')
            ->toJson();
    }
}
