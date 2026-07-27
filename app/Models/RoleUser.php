<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\RoleUserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\{
    Pivot,
    BelongsTo,
};

#[Fillable([
    'role_id',
    'user_id',
])]
class RoleUser extends Pivot
{
    /** @use HasFactory<RoleUserFactory> */
    use HasFactory;

    /**
     * {@inheritDoc}
     */
    public $table = 'role_users';

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
        ];
    }

    /**
     * Get the user that owns the RoleUser
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the role that owns the RoleUser
     *
     * @return BelongsTo<Role, $this>
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }
}
