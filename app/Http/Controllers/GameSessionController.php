<?php

namespace App\Http\Controllers;

use App\DTOs\GameSession\GameSessionDetails;
use App\Contracts\Services\GameSessionServiceInterface;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Http\Resources\GameSession\{
    GameSessionResource,
    GameSessionDetailsResource,
};
use App\Http\Requests\GameSession\{
    GameSessionFilterRequest,
    GameSessionDetailsRequest,
};
use Dedoc\Scramble\Attributes\{
    Group,
    Endpoint,
};

#[Group('Game sessions')]
class GameSessionController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param GameSessionServiceInterface $gameSessionService
     * @return void
     */
    public function __construct(
        private readonly GameSessionServiceInterface $gameSessionService,
    ) {
        //
    }

    /**
     * Get Game Session Feed
     *
     * Get all platform available game sessions feed.
     *
     * @return AnonymousResourceCollection
     */
    #[Endpoint(
        title: 'Get Game Session Feed',
        description: 'Get all platform available game sessions feed.',
    )]
    public function index(GameSessionFilterRequest $request): AnonymousResourceCollection
    {
        /** @var array<string, mixed> $data */
        $data = $request->validated();

        $venues = $this->gameSessionService->getFeed($data);

        return GameSessionResource::collection($venues);
    }

    /**
     * Get Game Session Details
     *
     * Get a game session details.
     *
     * @return GameSessionDetailsResource
     */
    #[Endpoint(
        title: 'Get Game Session Details',
        description: 'Get a game session details.',
    )]
    public function show(int $id, GameSessionDetailsRequest $request): GameSessionDetailsResource
    {
        /** @var array<string, string> $data */
        $data = $request->validated();

        $dto = GameSessionDetails::fromRequest($id, $data);

        $venue = $this->gameSessionService->getDetails($id, $dto);

        return GameSessionDetailsResource::make($venue);
    }
}
