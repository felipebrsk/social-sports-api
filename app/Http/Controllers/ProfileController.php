<?php

namespace App\Http\Controllers;

use App\DTOs\ProfileUpdate;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\Profile\ProfileUpdateRequest;
use App\Contracts\Services\Authentication\AuthContextServiceInterface;
use App\Contracts\Services\{
    ProfileServiceInterface,
    ResponseServiceInterface,
};
use Dedoc\Scramble\Attributes\{
    Group,
    Response,
    Endpoint,
};

#[Group('Profile')]
class ProfileController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param ResponseServiceInterface $response
     * @param ProfileServiceInterface $profileService
     * @param AuthContextServiceInterface $authContextService
     * @return void
     */
    public function __construct(
        private readonly ResponseServiceInterface $response,
        private readonly ProfileServiceInterface $profileService,
        private readonly AuthContextServiceInterface $authContextService,
    ) {
        //
    }

    /**
     * Update Profile
     *
     * Update the authenticated user profile.
     *
     * @param ProfileUpdateRequest $request
     * @return JsonResponse
     */
    #[Endpoint(
        title: 'Update Profile',
        description: 'Update the authenticated user profile.',
    )]
    #[Response(
        type: 'array{
            message: string
        }',
        examples: [[
            'message' => 'O seu perfil foi atualizado com sucesso!',
        ]],
    )]
    public function __invoke(ProfileUpdateRequest $request): JsonResponse
    {
        $uid = $this->authContextService->id();

        /** @var array<string, string> $data */
        $data = $request->validated();

        $dto = ProfileUpdate::fromRequest($uid, $data);

        $this->profileService->updateByUserId($dto);

        return $this->response
            ->setMessage('O seu perfil foi atualizado com sucesso!')
            ->toJson();
    }
}
