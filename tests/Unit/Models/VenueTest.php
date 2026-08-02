<?php

namespace Tests\Unit\Models;

use App\Models\Venue;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Tests\Contracts\Models\{
    ShouldTestCasts,
    ShouldTestTable,
    BaseModelTesting,
    ShouldTestTraits,
    ShouldTestGuarded,
    ShouldTestRelations,
    ShouldTestFillables,
};

class VenueTest extends BaseModelTesting implements
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
        return Venue::class;
    }

    /**
     * @inheritDoc
     */
    public function test_fillable_attributes(): void
    {
        $fillable = [
            'name',
            'city',
            'state',
            'address',
            'latitude',
            'verified',
            'featured',
            'longitude',
            'neighborhood',
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
            'verified' => 'bool',
            'featured' => 'bool',
            'latitude' => 'float',
            'longitude' => 'float',
        ];

        $this->assertHasCasts($casts);
    }

    /**
     * @inheritDoc
     */
    public function test_relations_attributes(): void
    {
        $relations = [
            'media' => MorphMany::class,
            'sports' => BelongsToMany::class,
            'gameSessions' => HasMany::class,
            'managers' => BelongsToMany::class,
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
        $table = 'venues';

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

    /**
     * Test scope withDistance adds calculated distance column.
     *
     * @return void
     */
    public function test_with_distance_scope(): void
    {
        $latitude = -23.550520;
        $longitude = -46.633308;

        $query = Venue::query()->withDistance($latitude, $longitude);

        $expectedSql = 'select ( 6371 * acos( cos( radians(-23.55052) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(-46.633308) ) + sin( radians(-23.55052) ) * sin( radians( latitude ) ) ) ) AS distance_in_km from "venues"';

        $actualSql = preg_replace('/\s+/', ' ', trim($query->toSql()));

        $expectedSqlNormalized = preg_replace('/\s+/', ' ', trim($expectedSql));

        $this->assertEquals($expectedSqlNormalized, $actualSql);
    }

    /**
     * Test scope withinRadius filters query by distance using raw formula in where clause.
     *
     * @return void
     */
    public function test_within_radius_scope(): void
    {
        $radiusKm = 15.5;
        $latitude = -23.550520;
        $longitude = -46.633308;

        $query = Venue::query()->withinRadius($radiusKm, $latitude, $longitude);

        $expectedSql = 'select * from "venues" where ( 6371 * acos( cos( radians(-23.55052) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(-46.633308) ) + sin( radians(-23.55052) ) * sin( radians( latitude ) ) ) ) <= 15.5';

        $actualSql = preg_replace('/\s+/', ' ', trim($query->toSql()));
        $expectedSqlNormalized = preg_replace('/\s+/', ' ', trim($expectedSql));

        $this->assertEquals($expectedSqlNormalized, $actualSql);
    }
}
