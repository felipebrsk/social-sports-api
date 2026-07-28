<?php

namespace App\Http\Resources;

use App\Models\Sport;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Sport
 * @property-read int $ongoing_games_count
 * @property-read int $upcoming_games_count
 */
class SportResource extends JsonResource
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
            'icon' => $this->icon,
            'venues_count' => $this->venues_count,
            'ongoing_games_count' => $this->ongoing_games_count,
            'upcoming_games_count' => $this->upcoming_games_count,
        ];
    }
}
