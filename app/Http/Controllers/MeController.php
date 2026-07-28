<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use Dedoc\Scramble\Attributes\Group;
use App\Contracts\Services\Authentication\AuthContextServiceInterface;

#[Group('User')]
class MeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param AuthContextServiceInterface $authContextService
     * @return void
     */
    public function __construct(
        private readonly AuthContextServiceInterface $authContextService,
    ) {
        //
    }

    /**
     * Auth User Details
     *
     * Get the authenticated user details.
     *
     * @return UserResource
     */
    public function __invoke(): UserResource
    {
        $user = $this->authContextService->user();

        $user->loadMissing([
            'profile:id,bio,avatar,whatsapp,instagram,user_id',
        ]);

        return UserResource::make($user);
    }
}
