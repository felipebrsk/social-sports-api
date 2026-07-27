<?php

namespace Tests\Unit\Models;

use App\Models\GameSessionRequest;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Tests\Contracts\Models\{
    ShouldTestCasts,
    ShouldTestTable,
    BaseModelTesting,
    ShouldTestTraits,
    ShouldTestGuarded,
    ShouldTestRelations,
    ShouldTestFillables,
};

class GameSessionRequestTest extends BaseModelTesting implements
    ShouldTestCasts,
    ShouldTestTable,
    ShouldTestTraits,
    ShouldTestGuarded,
    ShouldTestFillables,
    ShouldTestRelations
{
    /**
     * @inheritDoc
     */
    public function model(): string
    {
        return GameSessionRequest::class;
    }

    /**
     * @inheritDoc
     */
    public function test_fillable_attributes(): void
    {
        $fillable = [
            'user_id',
            'game_session_id',
            'rejection_reason',
            'game_session_request_status_id',
        ];

        $this->assertHasFillables($fillable);
    }

    /**
     * @inheritDoc
     */
    public function test_casts_attributes(): void
    {
        $casts = [
            'id' => 'int',
            'user_id' => 'int',
            'game_session_id' => 'int',
            'game_session_request_status_id' => 'int',
        ];

        $this->assertHasCasts($casts);
    }

    /**
     * @inheritDoc
     */
    public function test_relations_attributes(): void
    {
        $relations = [
            'user' => BelongsTo::class,
            'status' => BelongsTo::class,
            'gameSession' => BelongsTo::class,
        ];

        $this->assertHasRelations($relations);
    }

    /**
     * @inheritDoc
     */
    public function test_guarded_attributes(): void
    {
        $guarded = [
            '*',
        ];

        $this->assertHasGuarded($guarded);
    }

    /**
     * @inheritDoc
     */
    public function test_table_attribute(): void
    {
        $table = 'game_session_requests';

        $this->assertHasTable($table);
    }

    /**
     * @inheritDoc
     */
    public function test_traits_attributes(): void
    {
        $traits = [
            HasFactory::class,
        ];

        $this->assertUsesTraits($traits);
    }
}
