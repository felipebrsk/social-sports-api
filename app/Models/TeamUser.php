<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\TeamUserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\{
    Pivot,
    BelongsTo,
};

#[Fillable([
    'user_id',
    'team_id',
    'role_id',
])]
class TeamUser extends Pivot
{
    /** @use HasFactory<TeamUserFactory> */
    use HasFactory;

    /**
     * {@inheritDoc}
     */
    public $table = 'team_users';

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
            'team_id' => 'int',
            'role_id' => 'int',
        ];
    }

    /**
     * Get the user that owns the TeamUser
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the team that owns the TeamUser
     *
     * @return BelongsTo<Team, $this>
     */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * Get the role that owns the TeamUser
     *
     * @return BelongsTo<Role, $this>
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }
}
