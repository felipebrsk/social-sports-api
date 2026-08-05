<?php

namespace App\Http\Resources\GameSession;

use App\Models\GameSession;
use Illuminate\Http\Request;
use App\Enums\GameSessionRequestStatusEnum;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin GameSession
 * @property-read int $is_organizer
 * @property-read int|null $distance_in_km
 * @property-read int $user_request_status_id
 * @property-read int $approved_requests_count
 * @property-read \Illuminate\Support\Carbon $start_time
 * @property-read \Illuminate\Support\Carbon|null $end_time
 */
class GameSessionDetailsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $approvedCount = $this->approved_requests_count ?? 0;
        $externalCount = $this->external_players_count ?? 0;
        $occupiedSlots = $approvedCount + $externalCount;
        $availableSlots = max(0, $this->max_players - $occupiedSlots);

        $hasTeams = !! $this->host_team_id;

        return [
            'id' => $this->id,
            'is_team_match' => $hasTeams,
            'featured' => $this->featured,
            'description' => $this->description,
            'end_time' => $this->end_time?->toIso8601String(),
            'start_time' => $this->start_time->toIso8601String(),
            'distance_in_km' => $this->when(
                $this->offsetExists('distance_in_km'),
                fn () => $this->distance_in_km,
                null,
            ),
            'is_organizer' => $this->when(
                $this->offsetExists('is_organizer'),
                fn () => (bool) $this->is_organizer,
                null,
            ),
            'user_request_status' => $this->when(
                $this->offsetExists('user_request_status_id'),
                fn () => GameSessionRequestStatusEnum::tryFrom($this->user_request_status_id)?->label(),
                null,
            ),
            'players' => [
                'max' => $this->max_players,
                'occupied' => $occupiedSlots,
                'available' => $availableSlots,
                'is_full' => $availableSlots === 0,
                'external_players_count' => $externalCount,
            ],
            'teams' => $this->when($hasTeams, fn () => [
                'host' => TeamResource::make($this->whenLoaded('hostTeam')),
                'visitor' => TeamResource::make($this->whenLoaded('visitorTeam')),
            ]),
            'sport' => SportResource::make($this->whenLoaded('sport')),
            'status' => StatusResource::make($this->whenLoaded('status')),
            'creator' => CreatorResource::make($this->whenLoaded('creator')),
            'venue' => VenueDetailsResource::make($this->whenLoaded('venue')),
            'skill_level' => SkillLevelResource::make($this->whenLoaded('skillLevel')),
            'social_links' => SocialLinkResource::collection($this->whenLoaded('socialLinks')),
            'approved_requests' => GameSessionRequestResource::collection($this->whenLoaded('requests')),
        ];
    }
}
