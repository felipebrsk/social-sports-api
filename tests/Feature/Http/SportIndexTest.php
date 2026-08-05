<?php

namespace Tests\Feature\Http;

use App\Models\Sport;
use Tests\Traits\Dummy\HasDummySport;
use Tests\Feature\BaseIntegrationTesting;
use Database\Seeders\GameSessionStatusSeeder;

class SportIndexTest extends BaseIntegrationTesting
{
    use HasDummySport;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            GameSessionStatusSeeder::class,
        ]);
    }

    /**
     * Get the route name.
     *
     * @return string
     */
    private function getRouteName(): string
    {
        return 'sports.index';
    }

    /**
     * Test if route can return ok.
     *
     * @return void
     */
    public function test_if_route_can_return_ok(): void
    {
        $this->getJson(route($this->getRouteName()))->assertOk();
    }

    /**
     * Test if can return correct sports json count.
     *
     * @return void
     */
    public function test_if_can_return_correct_sports_json_count(): void
    {
        $this->getJson(route($this->getRouteName()))->assertOk()->assertJsonCount(0, 'data');

        $this->createDummySports(2);

        $this->getJson(route($this->getRouteName()))->assertOk()->assertJsonCount(2, 'data');
    }

    /**
     * Test if can get correct sports json structure.
     *
     * @return void
     */
    public function test_if_can_get_correct_sports_json_structure(): void
    {
        $this->createDummySport();

        $this->getJson(route($this->getRouteName()))->assertOk()->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'icon',
                ],
            ],
        ]);
    }

    /**
     * Test if can get correct sports json data.
     *
     * @return void
     */
    public function test_if_can_get_correct_sports_json_data(): void
    {
        $sports = $this->createDummySports(2);

        $this->getJson(route($this->getRouteName()))->assertOk()->assertJson([
            'data' => $sports->map(function (Sport $sport) {
                return [
                    'id' => $sport->id,
                    'name' => $sport->name,
                    'icon' => $sport->icon,
                ];
            })->toArray(),
        ]);
    }
}
