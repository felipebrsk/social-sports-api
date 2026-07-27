<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\VenueManagerFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\{
    Pivot,
    BelongsTo,
};

#[Fillable([
    'user_id',
    'role_id',
    'venue_id',
])]
class VenueManager extends Pivot
{
    /** @use HasFactory<VenueManagerFactory> */
    use HasFactory;

    /**
     * {@inheritDoc}
     */
    public $table = 'venue_managers';

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
            'user_id' => 'int',
            'role_id' => 'int',
            'venue_id' => 'int',
        ];
    }

    /**
     * Get the user that owns the VenueManager
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the role that owns the VenueManager
     *
     * @return BelongsTo<Role, $this>
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Get the venue that owns the VenueManager
     *
     * @return BelongsTo<Venue, $this>
     */
    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class);
    }
}
