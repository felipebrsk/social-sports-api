<?php

declare(strict_types=1);

namespace App\Models;

use App\Casts\StorageUrl;
use Database\Factories\ProfileFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

#[Fillable([
    'bio',
    'avatar',
    'user_id',
    'whatsapp',
    'instagram',
])]
class Profile extends Model
{
    /** @use HasFactory<ProfileFactory> */
    use HasFactory;

    /**
     * {@inheritDoc}
     */
    protected function casts(): array
    {
        return [
            'user_id' => 'int',
            'avatar' => StorageUrl::class,
        ];
    }

    /**
     * Get the user that owns the Profile
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
