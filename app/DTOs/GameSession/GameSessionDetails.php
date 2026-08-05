<?php

namespace App\DTOs\GameSession;

class GameSessionDetails
{
    /**
     * Create a new DTO instance.
     *
     * @param int $id
     * @param float|null $latitude
     * @param float|null $longitude
     * @return void
     */
    public function __construct(
        public readonly int $id,
        public readonly ?float $latitude = null,
        public readonly ?float $longitude = null,
    ) {
        //
    }

    /**
     * Extract data from array.
     *
     * @param int $id
     * @param array<string, string|null> $data
     * @return self
     */
    public static function fromRequest(int $id, array $data): self
    {
        return new self(
            id: $id,
            latitude: isset($data['latitude']) ? (float) $data['latitude'] : null,
            longitude: isset($data['longitude']) ? (float) $data['longitude'] : null,
        );
    }
}
