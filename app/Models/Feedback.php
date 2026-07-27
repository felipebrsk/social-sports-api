<?php

namespace App\Models;

use Database\Factories\FeedbackFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\{
    Model,
    SoftDeletes,
};

#[Fillable([
    'title',
    'user_id',
    'description',
    'admin_notes',
    'feedbackable_id',
    'feedbackable_type',
    'feedback_status_id',
    'feedback_category_id',
])]
class Feedback extends Model
{
    /** @use HasFactory<FeedbackFactory> */
    use HasFactory;

    use SoftDeletes;

    /**
     * {@inheritDoc}
     */
    public $table = 'feedbacks';

    /**
     * {@inheritDoc}
     */
    protected function casts(): array
    {
        return [
            'user_id' => 'int',
            'feedbackable_id' => 'int',
            'feedback_status_id' => 'int',
            'feedback_category_id' => 'int',
        ];
    }

    /**
     * Get the user that owns the Feedback
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the status that owns the Feedback
     *
     * @return BelongsTo<FeedbackStatus, $this>
     */
    public function status(): BelongsTo
    {
        return $this->belongsTo(FeedbackStatus::class, 'feedback_status_id');
    }

    /**
     * Get the category that owns the Feedback
     *
     * @return BelongsTo<FeedbackCategory, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(FeedbackCategory::class, 'feedback_category_id');
    }
}
