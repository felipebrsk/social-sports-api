<?php

namespace Tests\Unit\Filters\GameSession;

use Tests\TestCase;
use App\Models\GameSession;
use Illuminate\Support\Carbon;
use App\Filters\GameSession\GameSessionSearchFilter;
use App\Enums\{
    GameSessionStatusEnum,
    GameSessionRequestStatusEnum,
};

class GameSessionSearchFilterTest extends TestCase
{
    /**
     * Test applies default conditions and orderings when no filters.
     */
    public function test_applies_default_conditions_and_orderings_when_no_filters(): void
    {
        Carbon::setTestNow('2026-08-03 16:00:00');

        $filter = new GameSessionSearchFilter([]);

        $query = $filter->apply(GameSession::query());

        $expectedSql = 'select "game_sessions".*, '
            . '(select "featured" from "venues" where "venues"."id" = "game_sessions"."venue_id" limit 1) as "venue_featured", '
            . '(select "verified" from "venues" where "venues"."id" = "game_sessions"."venue_id" limit 1) as "venue_verified" '
            . 'from "game_sessions" '
            . 'where "game_sessions"."end_time" > ? '
            . 'and "game_sessions"."game_session_status_id" not in (?, ?) '
            . 'order by "game_sessions"."featured" desc, "venue_featured" desc, "venue_verified" desc, "game_sessions"."start_time" asc, "game_sessions"."id" asc';

        $this->assertEquals($expectedSql, $this->normalizeSql($query->toSql()));
        $this->assertEquals([
            '2026-08-03 16:00:00',
            GameSessionStatusEnum::FINISHED->value,
            GameSessionStatusEnum::CANCELLED->value,
        ], $query->getBindings());

        Carbon::setTestNow();
    }

    /**
     * Test applies subselects when authenticated user ID is provided.
     */
    public function test_applies_auth_user_subselects_when_user_id_is_provided(): void
    {
        Carbon::setTestNow('2026-08-03 16:00:00');

        $authUserId = 10;
        $filter = new GameSessionSearchFilter([], $authUserId);

        $query = $filter->apply(GameSession::query());

        $expectedSql = 'select CASE WHEN game_sessions.creator_id = ? THEN 1 ELSE 0 END AS is_organizer, '
            . '(select "game_session_request_status_id" as "user_request_status_id" from "game_session_requests" where "game_session_requests"."game_session_id" = "game_sessions"."id" and "game_session_requests"."user_id" = ? limit 1) as "user_request_status_id", '
            . '(select "featured" from "venues" where "venues"."id" = "game_sessions"."venue_id" limit 1) as "venue_featured", '
            . '(select "verified" from "venues" where "venues"."id" = "game_sessions"."venue_id" limit 1) as "venue_verified" '
            . 'from "game_sessions" '
            . 'where "game_sessions"."end_time" > ? '
            . 'and "game_sessions"."game_session_status_id" not in (?, ?) '
            . 'order by "game_sessions"."featured" desc, "venue_featured" desc, "venue_verified" desc, "game_sessions"."start_time" asc, "game_sessions"."id" asc';

        $this->assertEquals($expectedSql, $this->normalizeSql($query->toSql()));
        $this->assertEquals([
            $authUserId,
            $authUserId,
            '2026-08-03 16:00:00',
            GameSessionStatusEnum::FINISHED->value,
            GameSessionStatusEnum::CANCELLED->value,
        ], $query->getBindings());

        Carbon::setTestNow();
    }

    /**
     * Test applies sport_id, skill_level_id, and date filters when provided.
     */
    public function test_applies_sport_skill_level_and_date_filters(): void
    {
        Carbon::setTestNow('2026-08-03 16:00:00');

        $filter = new GameSessionSearchFilter([
            'sport_id' => 3,
            'skill_level_id' => 2,
            'date' => '2026-08-05',
        ]);

        $query = $filter->apply(GameSession::query());

        $expectedSql = 'select "game_sessions".*, '
            . '(select "featured" from "venues" where "venues"."id" = "game_sessions"."venue_id" limit 1) as "venue_featured", '
            . '(select "verified" from "venues" where "venues"."id" = "game_sessions"."venue_id" limit 1) as "venue_verified" '
            . 'from "game_sessions" '
            . 'where "game_sessions"."sport_id" = ? '
            . 'and "game_sessions"."skill_level_id" = ? '
            . 'and strftime(\'%Y-%m-%d\', "game_sessions"."start_time") = cast(? as text) '
            . 'and "game_sessions"."end_time" > ? '
            . 'and "game_sessions"."game_session_status_id" not in (?, ?) '
            . 'order by "game_sessions"."featured" desc, "venue_featured" desc, "venue_verified" desc, "game_sessions"."start_time" asc, "game_sessions"."id" asc';

        $this->assertEquals($expectedSql, $this->normalizeSql($query->toSql()));
        $this->assertEquals([
            3,
            2,
            '2026-08-05',
            '2026-08-03 16:00:00',
            GameSessionStatusEnum::FINISHED->value,
            GameSessionStatusEnum::CANCELLED->value,
        ], $query->getBindings());

        Carbon::setTestNow();
    }

    /**
     * Test applies search condition across description, venue fields, and sport name.
     */
    public function test_applies_search_condition_across_relations(): void
    {
        Carbon::setTestNow('2026-08-03 16:00:00');

        $searchTerm = 'Futebol';
        $filter = new GameSessionSearchFilter([
            'search' => $searchTerm,
        ]);

        $query = $filter->apply(GameSession::query());

        $expectedSql = 'select "game_sessions".*, '
            . '(select "featured" from "venues" where "venues"."id" = "game_sessions"."venue_id" limit 1) as "venue_featured", '
            . '(select "verified" from "venues" where "venues"."id" = "game_sessions"."venue_id" limit 1) as "venue_verified" '
            . 'from "game_sessions" '
            . 'where "game_sessions"."end_time" > ? '
            . 'and "game_sessions"."game_session_status_id" not in (?, ?) '
            . 'and ("game_sessions"."description" LIKE ? '
            . 'or exists (select * from "venues" where "game_sessions"."venue_id" = "venues"."id" and ("name" LIKE ? or "neighborhood" LIKE ? or "city" LIKE ? or "state" LIKE ?)) '
            . 'or exists (select * from "sports" where "game_sessions"."sport_id" = "sports"."id" and "name" LIKE ?)) '
            . 'order by "game_sessions"."featured" desc, "venue_featured" desc, "venue_verified" desc, "game_sessions"."start_time" asc, "game_sessions"."id" asc';

        $this->assertEquals($expectedSql, $this->normalizeSql($query->toSql()));
        $this->assertEquals([
            '2026-08-03 16:00:00',
            GameSessionStatusEnum::FINISHED->value,
            GameSessionStatusEnum::CANCELLED->value,
            "%{$searchTerm}%",
            "%{$searchTerm}%",
            "%{$searchTerm}%",
            "%{$searchTerm}%",
            "%{$searchTerm}%",
            "%{$searchTerm}%",
        ], $query->getBindings());

        Carbon::setTestNow();
    }

    /**
     * Test applies geolocation filter with custom radius_km and distance calculation select.
     */
    public function test_applies_geolocation_filter_and_distance_ordering(): void
    {
        Carbon::setTestNow('2026-08-03 16:00:00');

        $lat = -12.9714;
        $lng = -38.5014;
        $radiusKm = 20.0;

        $filter = new GameSessionSearchFilter([
            'latitude' => $lat,
            'longitude' => $lng,
            'radius_km' => $radiusKm,
        ]);

        $query = $filter->apply(GameSession::query());

        $expectedSql = 'select "game_sessions".*, '
            . '(select "featured" from "venues" where "venues"."id" = "game_sessions"."venue_id" limit 1) as "venue_featured", '
            . '(select "verified" from "venues" where "venues"."id" = "game_sessions"."venue_id" limit 1) as "venue_verified", '
            . '( 6371 * acos( cos( radians(?) ) * cos( radians( (SELECT latitude FROM venues WHERE id = game_sessions.venue_id) ) ) * cos( radians( (SELECT longitude FROM venues WHERE id = game_sessions.venue_id) ) - radians(?) ) + sin( radians(?) ) * sin( radians( (SELECT latitude FROM venues WHERE id = game_sessions.venue_id) ) ) ) ) AS distance_in_km '
            . 'from "game_sessions" '
            . 'where "game_sessions"."end_time" > ? '
            . 'and "game_sessions"."game_session_status_id" not in (?, ?) '
            . 'and exists (select * from "venues" where "game_sessions"."venue_id" = "venues"."id" and ( 6371 * acos( cos( radians(-12.9714) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(-38.5014) ) + sin( radians(-12.9714) ) * sin( radians( latitude ) ) ) ) <= 20) '
            . 'order by "game_sessions"."featured" desc, "venue_featured" desc, "venue_verified" desc, "distance_in_km" asc, "game_sessions"."start_time" asc, "game_sessions"."id" asc';

        $this->assertEquals($expectedSql, $this->normalizeSql($query->toSql()));
        $this->assertEquals([
            $lat,
            $lng,
            $lat,
            '2026-08-03 16:00:00',
            GameSessionStatusEnum::FINISHED->value,
            GameSessionStatusEnum::CANCELLED->value,
        ], $query->getBindings());

        Carbon::setTestNow();
    }

    /**
     * Test applies only available game sessions filter.
     */
    public function test_applies_only_available_game_sessions_filter(): void
    {
        Carbon::setTestNow('2026-08-03 16:00:00');

        $filter = new GameSessionSearchFilter([
            'only_available' => true,
        ]);

        $query = $filter->apply(GameSession::query());

        $expectedSql = 'select "game_sessions".*, '
            . '(select "featured" from "venues" where "venues"."id" = "game_sessions"."venue_id" limit 1) as "venue_featured", '
            . '(select "verified" from "venues" where "venues"."id" = "game_sessions"."venue_id" limit 1) as "venue_verified" '
            . 'from "game_sessions" '
            . 'where (game_sessions.max_players - game_sessions.external_players_count) > ( SELECT COUNT(*) FROM game_session_requests WHERE game_session_requests.game_session_id = game_sessions.id AND game_session_requests.game_session_request_status_id = ? ) '
            . 'and "game_sessions"."end_time" > ? '
            . 'and "game_sessions"."game_session_status_id" not in (?, ?) '
            . 'order by "game_sessions"."featured" desc, "venue_featured" desc, "venue_verified" desc, "game_sessions"."start_time" asc, "game_sessions"."id" asc';

        $this->assertEquals($expectedSql, $this->normalizeSql($query->toSql()));
        $this->assertEquals([
            GameSessionRequestStatusEnum::APPROVED->value,
            '2026-08-03 16:00:00',
            GameSessionStatusEnum::FINISHED->value,
            GameSessionStatusEnum::CANCELLED->value,
        ], $query->getBindings());

        Carbon::setTestNow();
    }

    /**
     * Helper to normalize spaces and breaklines on SQL instruction.
     *
     * @param string $sql
     * @return string
     */
    private function normalizeSql(string $sql): string
    {
        /** @var string $normalized */
        $normalized = preg_replace('/\s+/', ' ', trim($sql));

        return $normalized;
    }
}
