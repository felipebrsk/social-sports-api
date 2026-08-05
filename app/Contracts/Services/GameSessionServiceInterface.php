<?php

namespace App\Contracts\Services;

use App\Models\GameSession;
use App\DTOs\GameSession\GameSessionDetails;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * @extends AbstractServiceInterface<GameSession>
 */
interface GameSessionServiceInterface extends AbstractServiceInterface
{
    /**
     * Get the game sessions feed.
     *
     * @param array<string, mixed> $params
     * @return LengthAwarePaginator<int, GameSession>
     */
    public function getFeed(array $params): LengthAwarePaginator;

    /**
     * Get game session details.
     *
     * @param int $id
     * @param GameSessionDetails $data
     * @return GameSession
     */
    public function getDetails(int $id, GameSessionDetails $data): GameSession;
}
