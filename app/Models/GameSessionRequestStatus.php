<?php

namespace App\Models;

use App\Traits\HasSlug;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Database\Factories\GameSessionRequestStatusFactory;

#[Fillable([
    'name',
    'slug',
])]
class GameSessionRequestStatus extends Model
{
    /** @use HasFactory<GameSessionRequestStatusFactory> */
    use HasFactory;

    use HasSlug;

    /**
     * Get all of the requests for the GameSessionRequestStatus
     *
     * @return HasMany<GameSessionRequest, $this>
     */
    public function requests(): HasMany
    {
        return $this->hasMany(GameSessionRequest::class);
    }
}
