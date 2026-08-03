<?php

namespace App\Http\Resources\Venue;

use App\Models\GameSession;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin GameSession
 * @property-read int $approved_requests_count
 */
class GameSessionResource extends JsonResource
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
            'end_time' => $this->end_time,
            'start_time' => $this->start_time,
            'description' => $this->description,
            'max_players' => $this->max_players,
            'external_players_count' => $this->external_players_count,
            'approved_requests_count' => $this->approved_requests_count,
            'available_spots' => max(0, $this->max_players - ($this->approved_requests_count + $this->external_players_count)),
            'sport' => SportResource::make($this->whenLoaded('sport')),
            'creator' => CreatorResource::make($this->whenLoaded('creator')),
            'skill_level' => SkillLevelResource::make($this->whenLoaded('skillLevel')),
        ];
    }
}
