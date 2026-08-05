<?php

namespace App\Http\Resources\GameSession;

use Illuminate\Http\Request;
use App\Models\GameSessionStatus;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin GameSessionStatus
 */
class StatusResource extends JsonResource
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
        ];
    }
}
