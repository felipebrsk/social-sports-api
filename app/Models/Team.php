<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\TeamFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\{
    HasMany,
    BelongsTo,
    BelongsToMany,
};

#[Fillable([
    'name',
    'logo',
    'sport_id',
    'leader_id',
    'description',
])]
class Team extends Model
{
    /** @use HasFactory<TeamFactory> */
    use HasFactory;

    /**
     * {@inheritDoc}
     */
    protected function casts(): array
    {
        return [
            'sport_id' => 'int',
            'leader_id' => 'int',
        ];
    }

    /**
     * Get the sport that owns the Team
     *
     * @return BelongsTo<Sport, $this>
     */
    public function sport(): BelongsTo
    {
        return $this->belongsTo(Sport::class);
    }

    /**
     * Get the leader that owns the Team
     *
     * @return BelongsTo<User, $this>
     */
    public function leader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'leader_id');
    }

    /**
     * Get all of the gameSessions for the Team
     *
     * @return HasMany<GameSession, $this>
     */
    public function gameSessions(): HasMany
    {
        return $this->hasMany(GameSession::class);
    }

    /**
     * The users that belong to the Team
     *
     * @return BelongsToMany<User, $this, TeamUser>
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'team_users')
            ->using(TeamUser::class)
            ->withPivot(['role_id'])
            ->withTimestamps();
    }
}
