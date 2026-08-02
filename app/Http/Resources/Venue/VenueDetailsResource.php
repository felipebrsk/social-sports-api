<?php

namespace App\Http\Resources\Venue;

use App\Models\Venue;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Venue
 * @property-read int|null $distance_in_km
 */
class VenueDetailsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'city' => $this->city,
            'state' => $this->state,
            'address' => $this->address,
            'latitude' => $this->latitude,
            'verified' => $this->verified,
            'featured' => $this->featured,
            'longitude' => $this->longitude,
            'neighborhood' => $this->neighborhood,
            'distance_in_km' => $this->when(
                $this->offsetExists('distance_in_km'),
                fn () => $this->distance_in_km,
                null,
            ),
            'sports' => SportResource::collection($this->whenLoaded('sports')),
            'game_sessions' => GameSessionResource::collection($this->whenLoaded('gameSessions')),
        ];
    }
}
