<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Database\Factories\GameSessionRequestFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

#[Fillable([
    'user_id',
    'game_session_id',
    'rejection_reason',
    'game_session_request_status_id',
])]
class GameSessionRequest extends Model
{
    /** @use HasFactory<GameSessionRequestFactory> */
    use HasFactory;

    /**
     * {@inheritDoc}
     */
    protected function casts(): array
    {
        return [
            'user_id' => 'int',
            'game_session_id' => 'int',
            'game_session_request_status_id' => 'int',
        ];
    }

    /**
     * Get the user that owns the GameSessionRequest
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the gameSession that owns the GameSessionRequest
     *
     * @return BelongsTo<GameSession, $this>
     */
    public function gameSession(): BelongsTo
    {
        return $this->belongsTo(GameSession::class);
    }

    /**
     * Get the status that owns the GameSessionRequest
     *
     * @return BelongsTo<GameSessionRequestStatus, $this>
     */
    public function status(): BelongsTo
    {
        return $this->belongsTo(GameSessionRequestStatus::class, 'game_session_request_status_id');
    }
}
