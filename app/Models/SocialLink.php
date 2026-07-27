<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Database\Factories\SocialLinkFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\{
    MorphTo,
    BelongsTo,
};

#[Fillable([
    'url',
    'linkable_id',
    'linkable_type',
    'social_network_id',
])]
class SocialLink extends Model
{
    /** @use HasFactory<SocialLinkFactory> */
    use HasFactory;

    /**
     * {@inheritDoc}
     */
    protected function casts(): array
    {
        return [
            'linkable_id' => 'int',
            'social_network_id' => 'int',
        ];
    }

    /**
     * Get the linkable for the SocialLink
     *
     * @return MorphTo<Model, $this>
     */
    public function linkable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the socialNetwork that owns the SocialLink
     *
     * @return BelongsTo<SocialNetwork, $this>
     */
    public function socialNetwork(): BelongsTo
    {
        return $this->belongsTo(SocialNetwork::class);
    }
}
