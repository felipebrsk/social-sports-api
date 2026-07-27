<?php

namespace App\Http\Controllers\Authentication;

use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\DTOs\Authentication\ResetPassword;
use App\Contracts\Services\ResponseServiceInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Requests\Authentication\ResetPasswordRequest;
use App\Contracts\Services\Authentication\ResetPasswordServiceInterface;
use Dedoc\Scramble\Attributes\{
    Group,
    Response,
};
use App\Exceptions\Authentication\{
    InvalidTokenException,
    UserIsBlockedException,
};

#[Group('Authentication')]
class ResetPasswordController extends Controller
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
     * Reset Password
     *
     * Reset an user password.
     *
     * @param ResetPasswordRequest $request
     * @return JsonResponse
     * @throws InvalidTokenException
     * @throws ModelNotFoundException
     * @throws UserIsBlockedException
     */
    #[Response(
        type: 'array{
            message: string
        }',
        examples: [[
            'message' => 'A sua senha foi redefinida com sucesso!'
        ]],
    )]
    public function __invoke(ResetPasswordRequest $request): JsonResponse
    {
        /** @var array<string, string> $data */
        $data = $request->validated();

        $dto = ResetPassword::fromRequest($data);

        $this->resetPasswordService->resetPassword($dto);

        return $this->response
            ->setMessage('A sua senha foi redefinida com sucesso!')
            ->toJson();
    }
}
