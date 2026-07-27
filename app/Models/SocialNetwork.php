<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Database\Factories\SocialNetworkFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

#[Fillable([
    'name',
    'icon',
])]
class SocialNetwork extends Model
{
    /** @use HasFactory<SocialNetworkFactory> */
    use HasFactory;

    /**
     * Get all of the socialLinks for the SocialNetwork
     *
     * @return HasMany<SocialLink, $this>
     */
    public function socialLinks(): HasMany
    {
        return $this->hasMany(SocialLink::class);
    }
}
