<?php

namespace Tests\Unit\Filters\Venue;

use Mockery;
use Tests\TestCase;
use Illuminate\Database\Eloquent\Builder;
use App\Filters\Venue\CalculateDistanceFilter;

class CalculateDistanceFilterTest extends TestCase
{
    /**
     * Test if can correctly call withDistance Venue scope.
     *
     * @return void
     */
    public function test_if_can_correctly_call_withDistance_venue_scope(): void
    {
        $latitude = fake()->latitude();
        $longitude = fake()->longitude();

        $builder = Mockery::mock(Builder::class);
        $builder
            ->shouldReceive('withDistance')
            ->once()
            ->with($latitude, $longitude)
            ->andReturnSelf();

        $criteria = new CalculateDistanceFilter($latitude, $longitude);

        $result = $criteria->apply($builder);

        $this->assertSame($builder, $result);
    }
}
