<?php

namespace App\Http\Resources\Venue;

use App\Models\Venue;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Venue
 * @property-read int|null $distance_in_km
 * @property-read int $ongoing_games_count
 * @property-read int $upcoming_games_count
 */
class VenueResource extends JsonResource
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
            'verified' => $this->verified,
            'featured' => $this->featured,
            'neighborhood' => $this->neighborhood,
            'game_sessions_count' => $this->game_sessions_count,
            'ongoing_games_count' => $this->ongoing_games_count,
            'upcoming_games_count' => $this->upcoming_games_count,
            'distance_in_km' => $this->when(
                $this->offsetExists('distance_in_km'),
                fn () => $this->distance_in_km,
                null,
            ),
            'sports' => SportResource::collection($this->whenLoaded('sports')),
        ];
    }
}
