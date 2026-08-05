<?php

namespace Tests\Unit\Filters\GameSession;

use Tests\TestCase;
use App\Models\GameSession;
use App\Filters\GameSession\GameSessionDetailsCriteria;

class GameSessionDetailsCriteriaTest extends TestCase
{
    /**
     * Test does not modify query selects or bindings when no parameters are provided.
     */
    public function test_does_not_modify_query_when_no_parameters_provided(): void
    {
        $criteria = new GameSessionDetailsCriteria();

        $query = $criteria->apply(GameSession::query());

        $expectedSql = 'select * from "game_sessions"';

        $this->assertEquals($expectedSql, $this->normalizeSql($query->toSql()));
        $this->assertEmpty($query->getBindings());
    }

    /**
     * Test applies auth user subselects when authenticated user ID is provided.
     */
    public function test_applies_auth_user_subselects_when_user_id_is_provided(): void
    {
        $authUserId = 10;
        $criteria = new GameSessionDetailsCriteria(authUserId: $authUserId);

        $query = $criteria->apply(GameSession::query());

        $expectedSql = 'select CASE WHEN game_sessions.creator_id = ? THEN 1 ELSE 0 END AS is_organizer, '
            . '(select "game_session_request_status_id" from "game_session_requests" where "game_session_requests"."game_session_id" = "game_sessions"."id" and "game_session_requests"."user_id" = ? limit 1) as "user_request_status_id" '
            . 'from "game_sessions"';

        $this->assertEquals($expectedSql, $this->normalizeSql($query->toSql()));
        $this->assertEquals([$authUserId, $authUserId], $query->getBindings());
    }

    /**
     * Test applies distance formula select when latitude and longitude are provided.
     */
    public function test_applies_distance_select_when_coordinates_are_provided(): void
    {
        $lat = -12.9714;
        $lng = -38.5014;

        $criteria = new GameSessionDetailsCriteria(latitude: $lat, longitude: $lng);

        $query = $criteria->apply(GameSession::query());

        $expectedSql = 'select ( 6371 * acos( cos( radians(?) ) * cos( radians( (SELECT latitude FROM venues WHERE id = game_sessions.venue_id) ) ) '
            . '* cos( radians( (SELECT longitude FROM venues WHERE id = game_sessions.venue_id) ) - radians(?) ) + sin( radians(?) ) '
            . '* sin( radians( (SELECT latitude FROM venues WHERE id = game_sessions.venue_id) ) ) ) ) AS distance_in_km '
            . 'from "game_sessions"';

        $this->assertEquals($expectedSql, $this->normalizeSql($query->toSql()));
        $this->assertEquals([$lat, $lng, $lat], $query->getBindings());
    }

    /**
     * Test does not apply distance formula when only latitude is provided.
     */
    public function test_does_not_apply_distance_select_when_only_latitude_is_provided(): void
    {
        $criteria = new GameSessionDetailsCriteria(latitude: -12.9714);

        $query = $criteria->apply(GameSession::query());

        $expectedSql = 'select * from "game_sessions"';

        $this->assertEquals($expectedSql, $this->normalizeSql($query->toSql()));
        $this->assertEmpty($query->getBindings());
    }

    /**
     * Test applies both auth user subselects and distance select when all parameters are provided.
     */
    public function test_applies_all_selects_when_all_parameters_are_provided(): void
    {
        $authUserId = 5;
        $lat = -12.9714;
        $lng = -38.5014;

        $criteria = new GameSessionDetailsCriteria(
            authUserId: $authUserId,
            latitude: $lat,
            longitude: $lng
        );

        $query = $criteria->apply(GameSession::query());

        $expectedSql = 'select CASE WHEN game_sessions.creator_id = ? THEN 1 ELSE 0 END AS is_organizer, '
            . '(select "game_session_request_status_id" from "game_session_requests" where "game_session_requests"."game_session_id" = "game_sessions"."id" and "game_session_requests"."user_id" = ? limit 1) as "user_request_status_id", '
            . '( 6371 * acos( cos( radians(?) ) * cos( radians( (SELECT latitude FROM venues WHERE id = game_sessions.venue_id) ) ) '
            . '* cos( radians( (SELECT longitude FROM venues WHERE id = game_sessions.venue_id) ) - radians(?) ) + sin( radians(?) ) '
            . '* sin( radians( (SELECT latitude FROM venues WHERE id = game_sessions.venue_id) ) ) ) ) AS distance_in_km '
            . 'from "game_sessions"';

        $this->assertEquals($expectedSql, $this->normalizeSql($query->toSql()));
        $this->assertEquals([$authUserId, $authUserId, $lat, $lng, $lat], $query->getBindings());
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
