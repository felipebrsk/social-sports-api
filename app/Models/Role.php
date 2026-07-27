<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\RoleFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\{
    HasMany,
    BelongsToMany,
};

#[Fillable([
    'name',
    'description',
])]
class Role extends Model
{
    /** @use HasFactory<RoleFactory> */
    use HasFactory;

    /**
     * Get all venue managers associated with this role.
     *
     * @return HasMany<VenueManager, $this>
     */
    public function venueManagers(): HasMany
    {
        return $this->hasMany(VenueManager::class);
    }

    /**
     * Get all team users associated with this role.
     *
     * @return HasMany<TeamUser, $this>
     */
    public function teamUsers(): HasMany
    {
        return $this->hasMany(TeamUser::class);
    }

    /**
     * The users that belong to the Role
     *
     * @return BelongsToMany<User, $this, RoleUser>
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'role_users')
            ->using(RoleUser::class)
            ->withTimestamps();
    }
}
