<?php

namespace Tests\Unit\Models;

use App\Models\Sport;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Tests\Contracts\Models\{
    ShouldTestCasts,
    ShouldTestTable,
    BaseModelTesting,
    ShouldTestTraits,
    ShouldTestGuarded,
    ShouldTestRelations,
    ShouldTestFillables,
};

class SportTest extends BaseModelTesting implements
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
        return Sport::class;
    }

    /**
     * @inheritDoc
     */
    public function test_fillable_attributes(): void
    {
        $fillable = [
            'name',
            'icon',
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
        ];

        $this->assertHasCasts($casts);
    }

    /**
     * @inheritDoc
     */
    public function test_relations_attributes(): void
    {
        $relations = [
            'teams' => HasMany::class,
            'venues' => BelongsToMany::class,
            'gameSessions' => HasMany::class,
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
        $table = 'sports';

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
