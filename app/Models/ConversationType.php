<?php

namespace App\Models;

use App\Traits\HasSlug;
use Illuminate\Database\Eloquent\Model;
use Database\Factories\ConversationTypeFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

#[Fillable([
    'name',
    'slug',
    'description',
])]
class ConversationType extends Model
{
    /** @use HasFactory<ConversationTypeFactory> */
    use HasFactory;

    use HasSlug;

    /**
     * Get all of the conversations for the ConversationType
     *
     * @return HasMany<Conversation, $this>
     */
    public function conversations(): HasMany
    {
        return $this->hasMany(Conversation::class);
    }
}
