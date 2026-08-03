<?php

namespace Tests\Traits\Dummy;

use App\Models\GameSessionRequest;
use Illuminate\Database\Eloquent\Collection;

trait HasDummyGameSessionRequest
{
    /**
     * Create a generic game session.
     *
     * @param  array<string, mixed>  $data
     */
    public function createDummyGameSessionRequest(array $data = []): GameSessionRequest
    {
        return GameSessionRequest::factory()->create($data);
    }

    /**
     * Create multiple generic game sessions.
     *
     * @param  array<string, mixed>  $data
     * @return Collection<int, GameSessionRequest>
     */
    public function createDummyGameSessionRequests(int $count, array $data = []): Collection
    {
        return GameSessionRequest::factory($count)->create($data);
    }
}
