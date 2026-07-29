<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Database\Factories\VenueFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\{
    Model,
    Builder,
};
use Illuminate\Database\Eloquent\Attributes\{
    Hidden,
    Fillable,
};
use Illuminate\Database\Eloquent\Relations\{
    HasMany,
    MorphMany,
    BelongsToMany,
};

#[Fillable([
    'name',
    'city',
    'state',
    'address',
    'latitude',
    'verified',
    'featured',
    'longitude',
    'neighborhood',
])]
#[Hidden([
    'pivot',
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
            'featured' => 'bool',
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

    /**
     * Get all of the product's media.
     *
     * @return MorphMany<Media, $this>
     */
    public function media(): MorphMany
    {
        return $this->morphMany(Media::class, 'mediable');
    }

    /**
     * Scope to add calculated distance column in km.
     *
     * @param Builder<Venue> $query
     * @param float $latitude
     * @param float $longitude
     * @return Builder<Venue>
     */
    public function scopeWithDistance(Builder $query, float $latitude, float $longitude): Builder
    {
        /** @phpstan-ignore-next-line */
        return $query->addSelect(DB::raw("
            ( 6371 * acos( cos( radians({$latitude}) ) * cos( radians( latitude ) ) 
            * cos( radians( longitude ) - radians({$longitude}) ) + sin( radians({$latitude}) ) 
            * sin( radians( latitude ) ) ) ) AS distance_in_km
        "));
    }

    /**
     * Scope to filter by maximum radius in km.
     *
     * @param Builder<Venue> $query
     * @param float $radiusKm
     * @return Builder<Venue>
     */
    public function scopeWithinRadius(Builder $query, float $radiusKm): Builder
    {
        return $query->havingRaw('distance_in_km <= ?', [$radiusKm]);
    }
}
