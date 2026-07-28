<?php

namespace App\Http\Controllers;

use App\Http\Resources\SportResource;
use App\Contracts\Services\SportServiceInterface;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Dedoc\Scramble\Attributes\{
    Group,
    Endpoint,
};

#[Group('Sports')]
class SportController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param SportServiceInterface $sportService
     * @return void
     */
    public function __construct(
        private readonly SportServiceInterface $sportService,
    ) {
        //
    }

    /**
     * Get Platform Sports
     *
     * Get all platform available sports.
     *
     * @return AnonymousResourceCollection
     */
    #[Endpoint(
        title: 'Get Platform Sports',
        description: 'Get all platform available sports.',
    )]
    public function __invoke(): AnonymousResourceCollection
    {
        $sports = $this->sportService->all();

        return SportResource::collection($sports);
    }
}
