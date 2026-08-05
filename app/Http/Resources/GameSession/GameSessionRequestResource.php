<?php

namespace App\Http\Resources\GameSession;

use Illuminate\Http\Request;
use App\Models\GameSessionRequest;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin GameSessionRequest
 */
class GameSessionRequestResource extends JsonResource
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
            'user' => UserResource::make($this->whenLoaded('user')),
        ];
    }
}
