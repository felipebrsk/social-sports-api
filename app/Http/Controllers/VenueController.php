<?php

namespace App\Http\Controllers;

use App\Contracts\Services\VenueServiceInterface;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Http\Resources\Venue\{
    VenueResource,
    VenueDetailsResource,
};
use App\Http\Requests\Venue\{
    VenueFilterRequest,
    VenueDetailsRequest,
};
use Dedoc\Scramble\Attributes\{
    Group,
    Endpoint,
};

#[Group('Venues')]
class VenueController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param VenueServiceInterface $sportService
     * @return void
     */
    public function __construct(
        private readonly VenueServiceInterface $sportService,
    ) {
        //
    }

    /**
     * Get Platform Venues
     *
     * Get all platform available venues.
     *
     * @return AnonymousResourceCollection
     */
    #[Endpoint(
        title: 'Get Platform Venues',
        description: 'Get all platform available venues with filters.',
    )]
    public function index(VenueFilterRequest $request): AnonymousResourceCollection
    {
        /** @var array<string, mixed> $data */
        $data = $request->validated();

        $venues = $this->sportService->searchVenues($data);

        return VenueResource::collection($venues);
    }

    /**
     * Get Venue Details
     *
     * Get a venue details.
     *
     * @return VenueDetailsResource
     */
    #[Endpoint(
        title: 'Get Venue Details',
        description: 'Get a venue details.',
    )]
    public function show(int $id, VenueDetailsRequest $request): VenueDetailsResource
    {
        /** @var array<string, string|float> $data */
        $data = $request->validated();

        $venue = $this->sportService->getVenueDetails($id, $data);

        return VenueDetailsResource::make($venue);
    }
}
