<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\SportFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\{
    HasMany,
    BelongsToMany,
};

#[Fillable([
    'name',
    'icon',
])]
class Sport extends Model
{
    /** @use HasFactory<SportFactory> */
    use HasFactory;

    /**
     * Get all of the teams for the Sport
     *
     * @return HasMany<Team, $this>
     */
    public function teams(): HasMany
    {
        return $this->hasMany(Team::class);
    }

    /**
     * Get all of the gameSessions for the Sport
     *
     * @return HasMany<GameSession, $this>
     */
    public function gameSessions(): HasMany
    {
        return $this->hasMany(GameSession::class);
    }

    /**
     * The venues that belong to the Sport
     *
     * @return BelongsToMany<Venue, $this, VenueSport>
     */
    public function venues(): BelongsToMany
    {
        return $this->belongsToMany(Venue::class, 'venue_sports')
            ->using(VenueSport::class)
            ->withTimestamps();
    }
}
