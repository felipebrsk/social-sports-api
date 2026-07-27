<?php

namespace App\Models;

use App\Traits\HasSlug;
use Illuminate\Database\Eloquent\Model;
use Database\Factories\FeedbackCategoryFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

#[Fillable([
    'name',
    'slug',
])]
class FeedbackCategory extends Model
{
    /** @use HasFactory<FeedbackCategoryFactory> */
    use HasFactory;

    use HasSlug;

    /**
     * Get all of the feedbacks for the FeedbackCategory
     *
     * @return HasMany<Feedback, $this>
     */
    public function feedbacks(): HasMany
    {
        return $this->hasMany(Feedback::class);
    }
}
