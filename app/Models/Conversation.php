<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Database\Factories\ConversationFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\{
    HasMany,
    BelongsTo,
    BelongsToMany,
};

#[Fillable([
    'game_session_id',
    'conversation_type_id',
])]
class Conversation extends Model
{
    /** @use HasFactory<ConversationFactory> */
    use HasFactory;

    /**
     * {@inheritDoc}
     */
    protected function casts(): array
    {
        return [
            'game_session_id' => 'int',
            'conversation_type_id' => 'int',
        ];
    }

    /**
     * Get the gameSession that owns the Conversation
     *
     * @return BelongsTo<GameSession, $this>
     */
    public function gameSession(): BelongsTo
    {
        return $this->belongsTo(GameSession::class);
    }

    /**
     * Get the type that owns the Conversation
     *
     * @return BelongsTo<ConversationType, $this>
     */
    public function type(): BelongsTo
    {
        return $this->belongsTo(ConversationType::class, 'conversation_type_id');
    }

    /**
     * Get all of the messages for the Conversation
     *
     * @return HasMany<Message, $this>
     */
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    /**
     * The users that belong to the Conversation
     *
     * @return BelongsToMany<User, $this, ConversationUser>
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'conversation_users')
            ->using(ConversationUser::class)
            ->withPivot(['last_message', 'last_read_at'])
            ->withTimestamps();
    }
}
