<?php

namespace Tests\Traits\Dummy;

use App\Models\GameSession;
use Illuminate\Database\Eloquent\Collection;

trait HasDummyGameSession
{
    /**
     * Create a generic game session.
     *
     * @param  array<string, mixed>  $data
     */
    public function createDummyGameSession(array $data = []): GameSession
    {
        return GameSession::factory()->create($data);
    }

    /**
     * Create multiple generic game sessions.
     *
     * @param  array<string, mixed>  $data
     * @return Collection<int, GameSession>
     */
    public function createDummyGameSessions(int $count, array $data = []): Collection
    {
        return GameSession::factory($count)->create($data);
    }
}
