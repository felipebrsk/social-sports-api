<?php

namespace App\Models;

use App\Casts\StorageUrl;
use Database\Factories\MediaFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\{
    MorphTo,
    BelongsTo,
};
use Illuminate\Database\Eloquent\Attributes\{
    Table,
    Fillable,
};

#[Table('media')]
#[Fillable([
    'path',
    'alias',
    'mediable_id',
    'mediable_type',
    'media_type_id',
])]
class Media extends Model
{
    /** @use HasFactory<MediaFactory> */
    use HasFactory;

    /**
     * {@inheritDoc}
     */
    protected function casts(): array
    {
        return [
            'media_type_id' => 'int',
            'path' => StorageUrl::class,
        ];
    }

    /**
     * Get the mediable that owns the Media.
     *
     * @return MorphTo<Model, $this>
     */
    public function mediable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the type that owns the Media
     *
     * @return BelongsTo<MediaType, $this>
     */
    public function type(): BelongsTo
    {
        return $this->belongsTo(MediaType::class, 'media_type_id');
    }
}
