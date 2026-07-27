<?php

namespace App\Http\Controllers\Authentication;

use App\Http\Controllers\Controller;
use App\DTOs\Authentication\EmailVerify;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\Authentication\VerifyRequest;
use App\Contracts\Services\ResponseServiceInterface;
use App\Exceptions\Authentication\UserEmailAlreadyVerifiedException;
use App\Contracts\Services\Authentication\EmailVerifyServiceInterface;
use Illuminate\Http\{
    JsonResponse,
    RedirectResponse,
};
use Dedoc\Scramble\Attributes\{
    Endpoint,
    Group,
    Response as ScrambleResponse,
};

#[Group('Email Verification')]
class VerificationController extends Controller
{
    /**
     * Where to redirect users after verification.
     *
     * @var string
     */
    protected string $redirectTo = '/email/confirmado';

    /**
     * Create a new controller instance.
     *
     * @param ResponseServiceInterface $response
     * @param EmailVerifyServiceInterface $emailVerifyService
     * @return void
     */
    public function __construct(
        private readonly ResponseServiceInterface $response,
        private readonly EmailVerifyServiceInterface $emailVerifyService,
    ) {
        //
    }

    /**
     * Resend the email verification notification.
     *
     * @return JsonResponse
     * @throws UserEmailAlreadyVerifiedException
     */
    #[Endpoint(
        title: 'Send Verification Email',
        description: 'Send/resend the email verification notification for the authenticated user.',
    )]
    #[ScrambleResponse(
        status: 202,
        type: 'array{
            message: string
        }',
        examples: [[
            'message' => 'Um email de verificação foi enviado com sucesso!',
        ]],
    )]
    public function resend(): JsonResponse
    {
        $this->emailVerifyService->resend();

        return $this->response
            ->setMessage('Um email de verificação foi enviado com sucesso!')
            ->toJson(Response::HTTP_ACCEPTED);
    }

    /**
     * Mark the authenticated user's email address as verified.
     *
     * @param VerifyRequest $request
     * @return RedirectResponse
     */
    #[Endpoint(
        title: 'Verify Email',
        description: 'Mark the authenticated user"s email address as verified.',
    )]
    public function verify(VerifyRequest $request): RedirectResponse
    {
        /** @var array<string, string> $data */
        $data = $request->validated();

        $dto = EmailVerify::fromRequest($data);

        $this->emailVerifyService->verify($dto);

        return redirect()->to($this->redirectTo);
    }
}
