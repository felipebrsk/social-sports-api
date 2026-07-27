<?php

namespace Tests\Unit\Models;

use App\Models\GameSession;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Tests\Contracts\Models\{
    ShouldTestCasts,
    ShouldTestTable,
    BaseModelTesting,
    ShouldTestTraits,
    ShouldTestGuarded,
    ShouldTestRelations,
    ShouldTestFillables,
};

class GameSessionTest extends BaseModelTesting implements
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
        return GameSession::class;
    }

    /**
     * @inheritDoc
     */
    public function test_fillable_attributes(): void
    {
        $fillable = [
            'featured',
            'end_time',
            'sport_id',
            'venue_id',
            'start_time',
            'creator_id',
            'max_players',
            'description',
            'host_team_id',
            'skill_level_id',
            'visitor_team_id',
            'game_session_status_id',
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
            'sport_id' => 'int',
            'venue_id' => 'int',
            'featured' => 'bool',
            'creator_id' => 'int',
            'max_players' => 'int',
            'host_team_id' => 'int',
            'end_time' => 'datetime',
            'skill_level_id' => 'int',
            'visitor_team_id' => 'int',
            'start_time' => 'datetime',
            'game_session_status_id' => 'int',
        ];

        $this->assertHasCasts($casts);
    }

    /**
     * @inheritDoc
     */
    public function test_relations_attributes(): void
    {
        $relations = [
            'payment' => HasOne::class,
            'sport' => BelongsTo::class,
            'venue' => BelongsTo::class,
            'status' => BelongsTo::class,
            'requests' => HasMany::class,
            'creator' => BelongsTo::class,
            'hostTeam' => BelongsTo::class,
            'skillLevel' => BelongsTo::class,
            'visitorTeam' => BelongsTo::class,
            'conversations' => HasMany::class,
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
        $table = 'game_sessions';

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
