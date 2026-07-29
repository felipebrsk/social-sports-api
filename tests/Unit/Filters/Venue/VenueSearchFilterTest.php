<?php

namespace Tests\Unit\Filters\Venue;

use Tests\TestCase;
use App\Models\Venue;
use App\Filters\Venue\VenueSearchFilter;

class VenueSearchFilterTest extends TestCase
{
    /**
     * Test applies fallback ordering when no specific location filter is provided.
     */
    public function test_applies_fallback_default_ordering_when_no_filters(): void
    {
        $filter = new VenueSearchFilter([]);

        $query = $filter->apply(Venue::query());

        $expectedSql = 'select * from "venues" order by "featured" desc, "verified" desc, "created_at" desc';

        $this->assertEquals($expectedSql, $this->normalizeSql($query->toSql()));
    }

    /**
     * Test applies search condition in subquery across name, address, neighborhood, and city.
     */
    public function test_applies_search_condition_across_multiple_columns(): void
    {
        $searchTerm = 'Arena';

        $filter = new VenueSearchFilter([
            'search' => $searchTerm,
        ]);

        $query = $filter->apply(Venue::query());

        $expectedSql = 'select * from "venues" where ("name" LIKE ? or "address" LIKE ? or "neighborhood" LIKE ? or "city" LIKE ?) order by "featured" desc, "verified" desc, "created_at" desc';

        $this->assertEquals($expectedSql, $this->normalizeSql($query->toSql()));
        $this->assertEquals([
            "%{$searchTerm}%",
            "%{$searchTerm}%",
            "%{$searchTerm}%",
            "%{$searchTerm}%",
        ], $query->getBindings());
    }

    /**
     * Test applies sport_id filter using whereHas relation.
     */
    public function test_applies_sport_id_filter_using_where_has(): void
    {
        $sportId = 5;
        $filter = new VenueSearchFilter([
            'sport_id' => $sportId,
        ]);

        $query = $filter->apply(Venue::query());

        $expectedSql = 'select * from "venues" where exists (select * from "sports" inner join "venue_sports" on "sports"."id" = "venue_sports"."sport_id" where "venues"."id" = "venue_sports"."venue_id" and "sports"."id" = ?) order by "featured" desc, "verified" desc, "created_at" desc';

        $this->assertEquals($expectedSql, $this->normalizeSql($query->toSql()));
        $this->assertEquals([$sportId], $query->getBindings());
    }

    /**
     * Test applies geolocation filter with custom radius_km and distance ordering.
     */
    public function test_applies_geolocation_filter_with_custom_radius_km(): void
    {
        $lat = -23.550520;
        $lng = -46.633308;
        $radiusKm = 20.0;

        $filter = new VenueSearchFilter([
            'latitude' => $lat,
            'longitude' => $lng,
            'radius_km' => $radiusKm,
        ]);

        $query = $filter->apply(Venue::query());

        $expectedSql = 'select ( 6371 * acos( cos( radians(-23.55052) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(-46.633308) ) + sin( radians(-23.55052) ) * sin( radians( latitude ) ) ) ) AS distance_in_km from "venues" having "distance_in_km" <= ? order by "featured" desc, "verified" desc, "distance_in_km" asc';

        $this->assertEquals($expectedSql, $this->normalizeSql($query->toSql()));
        $this->assertEquals([$radiusKm], $query->getBindings());
    }

    /**
     * Test applies geolocation filter with default radius_km when not supplied.
     */
    public function test_applies_geolocation_filter_using_default_radius_km(): void
    {
        $lat = -12.9714;
        $lng = -38.5014;

        $filter = new VenueSearchFilter([
            'latitude' => $lat,
            'longitude' => $lng,
        ]);

        $query = $filter->apply(Venue::query());

        $this->assertEquals([15.0], $query->getBindings());
    }

    /**
     * Test applies city filter without state condition.
     */
    public function test_applies_city_filter_without_state(): void
    {
        $city = 'Salvador';
        $filter = new VenueSearchFilter([
            'city' => $city,
        ]);

        $query = $filter->apply(Venue::query());

        $expectedSql = 'select * from "venues" where "city" LIKE ? order by "featured" desc, "verified" desc, "name" asc';

        $this->assertEquals($expectedSql, $this->normalizeSql($query->toSql()));
        $this->assertEquals(["%{$city}%"], $query->getBindings());
    }

    /**
     * Test applies city and state filters together and transforms state to uppercase.
     */
    public function test_applies_city_and_state_filters_with_uppercase_state(): void
    {
        $city = 'Feira de Santana';
        $state = 'ba';

        $filter = new VenueSearchFilter([
            'city' => $city,
            'state' => $state,
        ]);

        $query = $filter->apply(Venue::query());

        $expectedSql = 'select * from "venues" where "city" LIKE ? and "state" = ? order by "featured" desc, "verified" desc, "name" asc';

        $this->assertEquals($expectedSql, $this->normalizeSql($query->toSql()));
        $this->assertEquals(["%{$city}%", 'BA'], $query->getBindings());
    }

    /**
     * Test combines search, sport_id, and geolocation filters simultaneously.
     */
    public function test_combines_all_filter_branches_together(): void
    {
        $filter = new VenueSearchFilter([
            'search' => 'Beach',
            'sport_id' => 3,
            'latitude' => -12.9714,
            'longitude' => -38.5014,
            'radius_km' => 10,
        ]);

        $query = $filter->apply(Venue::query());

        $expectedSql = 'select ( 6371 * acos( cos( radians(-12.9714) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(-38.5014) ) + sin( radians(-12.9714) ) * sin( radians( latitude ) ) ) ) AS distance_in_km from "venues" where ("name" LIKE ? or "address" LIKE ? or "neighborhood" LIKE ? or "city" LIKE ?) and exists (select * from "sports" inner join "venue_sports" on "sports"."id" = "venue_sports"."sport_id" where "venues"."id" = "venue_sports"."venue_id" and "sports"."id" = ?) having "distance_in_km" <= ? order by "featured" desc, "verified" desc, "distance_in_km" asc';

        $this->assertEquals($expectedSql, $this->normalizeSql($query->toSql()));
        $this->assertEquals([
            '%Beach%',
            '%Beach%',
            '%Beach%',
            '%Beach%',
            3,
            10.0,
        ], $query->getBindings());
    }

    /**
     * Helper to normalize spaces and breaklines on SQL instruction.
     *
     * @param string $sql
     * @return string
     */
    private function normalizeSql(string $sql): string
    {
        return preg_replace('/\s+/', ' ', trim($sql));
    }
}
