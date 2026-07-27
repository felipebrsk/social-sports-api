<?php

namespace Tests\Feature\Http;

use Tests\Feature\BaseIntegrationTesting;

class MeTest extends BaseIntegrationTesting
{
    /**
     * Get the route name.
     *
     * @return string
     */
    private function getRouteName(): string
    {
        return 'user.me';
    }

    /**
     * Test if can't get me details if not authenticated.
     *
     * @return void
     */
    public function test_if_cant_get_me_details_if_not_authenticated(): void
    {
        $this->actingAsGuest();

        $this->getJson(route($this->getRouteName()))
            ->assertUnauthorized()
            ->assertSee('Unauthenticated.');
    }

    /**
     * Test if can get me details.
     *
     * @return void
     */
    public function test_if_can_get_me_details(): void
    {
        $this->getJson(route($this->getRouteName()))->assertOk();
    }

    /**
     * Test if can get correct attributes count.
     *
     * @return void
     */
    public function test_if_can_get_correct_attributes_count(): void
    {
        $this->getJson(route($this->getRouteName()))->assertOk()->assertJsonCount(5, 'data');
    }

    /**
     * Test if can get correct json structure.
     *
     * @return void
     */
    public function test_if_can_get_correct_json_structure(): void
    {
        $this->getJson(route($this->getRouteName()))->assertOk()->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'email',
                'created_at',
                'updated_at',
            ],
        ]);
    }

    /**
     * Test if can get correct json data.
     *
     * @return void
     */
    public function test_if_can_get_correct_json_data(): void
    {
        $this->getJson(route($this->getRouteName()))->assertOk()->assertJson([
            'data' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email,
                'created_at' => $this->user->created_at?->toISOString(),
                'updated_at' => $this->user->updated_at?->toISOString(),
            ],
        ]);
    }
}
