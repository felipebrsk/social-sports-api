<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Database\Factories\MediaTypeFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

#[Fillable([
    'name',
])]
class MediaType extends Model
{
    /** @use HasFactory<MediaTypeFactory> */
    use HasFactory;

    /**
     * Get all of the media for the MediaType
     *
     * @return HasMany<Media>
     */
    public function media(): HasMany
    {
        return $this->hasMany(Media::class);
    }
}
