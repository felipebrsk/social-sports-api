<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Database\Factories\GameSessionFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\{
    HasOne,
    HasMany,
    BelongsTo,
    MorphMany,
};

#[Fillable([
    'featured',
    'end_time',
    'sport_id',
    'venue_id',
    'start_time',
    'creator_id',
    'max_players',
    'description',
    'host_team_id',
    'skill_level_id',
    'visitor_team_id',
    'external_players_count',
    'game_session_status_id',
])]
class GameSession extends Model
{
    /** @use HasFactory<GameSessionFactory> */
    use HasFactory;

    /**
     * {@inheritDoc}
     */
    protected function casts(): array
    {
        return [
            'sport_id' => 'int',
            'venue_id' => 'int',
            'featured' => 'bool',
            'creator_id' => 'int',
            'max_players' => 'int',
            'host_team_id' => 'int',
            'end_time' => 'datetime',
            'skill_level_id' => 'int',
            'visitor_team_id' => 'int',
            'start_time' => 'datetime',
            'external_players_count' => 'int',
            'game_session_status_id' => 'int',
        ];
    }

    /**
     * Get the sport that owns the GameSession
     *
     * @return BelongsTo<Sport, $this>
     */
    public function sport(): BelongsTo
    {
        return $this->belongsTo(Sport::class);
    }

    /**
     * Get the venue that owns the GameSession
     *
     * @return BelongsTo<Venue, $this>
     */
    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class);
    }

    /**
     * Get the skillLevel that owns the GameSession
     *
     * @return BelongsTo<SkillLevel, $this>
     */
    public function skillLevel(): BelongsTo
    {
        return $this->belongsTo(SkillLevel::class);
    }

    /**
     * Get the creator that owns the GameSession
     *
     * @return BelongsTo<User, $this>
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    /**
     * Get the hostTeam that owns the GameSession
     *
     * @return BelongsTo<Team, $this>
     */
    public function hostTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'host_team_id');
    }

    /**
     * Get the visitorTeam that owns the GameSession
     *
     * @return BelongsTo<Team, $this>
     */
    public function visitorTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'visitor_team_id');
    }

    /**
     * Get the status that owns the GameSession
     *
     * @return BelongsTo<GameSessionStatus, $this>
     */
    public function status(): BelongsTo
    {
        return $this->belongsTo(GameSessionStatus::class, 'game_session_status_id');
    }

    /**
     * Get the payment associated with the GameSession
     *
     * @return HasOne<Payment, $this>
     */
    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    /**
     * Get all of the conversations for the GameSession
     *
     * @return HasMany<Conversation, $this>
     */
    public function conversations(): HasMany
    {
        return $this->hasMany(Conversation::class);
    }

    /**
     * Get all of the requests for the GameSession
     *
     * @return HasMany<GameSessionRequest, $this>
     */
    public function requests(): HasMany
    {
        return $this->hasMany(GameSessionRequest::class);
    }

    /**
     * Get all of the social links for the GameSession
     *
     * @return MorphMany<SocialLink, $this>
     */
    public function socialLinks(): MorphMany
    {
        return $this->morphMany(SocialLink::class, 'linkable');
    }
}
