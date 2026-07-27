<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\VenueSportFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\{
    Pivot,
    BelongsTo,
};

#[Fillable([
    'sport_id',
    'venue_id',
])]
class VenueSport extends Pivot
{
    /** @use HasFactory<VenueSportFactory> */
    use HasFactory;

    /**
     * {@inheritDoc}
     */
    public $table = 'venue_sports';

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
            'sport_id' => 'int',
            'venue_id' => 'int',
        ];
    }

    /**
     * Get the venue that owns the VenueSport
     *
     * @return BelongsTo<Venue, $this>
     */
    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class);
    }

    /**
     * Get the sport that owns the VenueSport
     *
     * @return BelongsTo<Sport, $this>
     */
    public function sport(): BelongsTo
    {
        return $this->belongsTo(Sport::class);
    }
}
