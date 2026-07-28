<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\VenueFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\{
    HasMany,
    BelongsToMany,
};

#[Fillable([
    'name',
    'city',
    'state',
    'address',
    'latitude',
    'verified',
    'longitude',
    'neighborhood',
])]
class Venue extends Model
{
    /** @use HasFactory<VenueFactory> */
    use HasFactory;

    /**
     * {@inheritDoc}
     */
    protected function casts(): array
    {
        return [
            'verified' => 'bool',
            'latitude' => 'float',
            'longitude' => 'float',
        ];
    }

    /**
     * Get all of the gameSessions for the Venue
     *
     * @return HasMany<GameSession, $this>
     */
    public function gameSessions(): HasMany
    {
        return $this->hasMany(GameSession::class);
    }

    /**
     * The sports that belong to the Venue
     *
     * @return BelongsToMany<Sport, $this, VenueSport>
     */
    public function sports(): BelongsToMany
    {
        return $this->belongsToMany(Sport::class, 'venue_sports')
            ->using(VenueSport::class)
            ->withTimestamps();
    }

    /**
     * The managers that belong to the Venue
     *
     * @return BelongsToMany<User, $this, VenueManager>
     */
    public function managers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'venue_managers')
            ->using(VenueManager::class)
            ->withPivot(['role_id'])
            ->withTimestamps();
    }
}
