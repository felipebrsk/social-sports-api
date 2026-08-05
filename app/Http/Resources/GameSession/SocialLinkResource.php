<?php

namespace App\Http\Resources\GameSession;

use App\Models\SocialLink;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin SocialLink
 */
class SocialLinkResource extends JsonResource
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
            'url' => $this->url,
            'network' => SocialNetworkResource::make($this->whenLoaded('socialNetwork')),
        ];
    }
}
