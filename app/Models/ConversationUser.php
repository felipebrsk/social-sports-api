<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\ConversationUserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\{
    Pivot,
    BelongsTo,
};

#[Fillable([
    'user_id',
    'last_message',
    'last_read_at',
    'conversation_id',
])]
class ConversationUser extends Pivot
{
    /** @use HasFactory<ConversationUserFactory> */
    use HasFactory;

    /**
     * {@inheritDoc}
     */
    public $table = 'conversation_users';

    /**
     * {@inheritDoc}
     */
    protected $guarded = [
        '*',
    ];

    /**
     * {@inheritDoc}
     */
    protected function casts(): array
    {
        return [
            'id' => 'int',
            'user_id' => 'int',
            'conversation_id' => 'int',
            'last_read_at' => 'datetime',
        ];
    }

    /**
     * Get the user that owns the ConversationUser
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the conversation that owns the ConversationUser
     *
     * @return BelongsTo<Conversation, $this>
     */
    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }
}
