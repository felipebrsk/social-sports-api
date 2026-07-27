<?php

namespace Tests\Feature\Http\Authentication;

use Tests\Feature\BaseIntegrationTesting;

class LoginTest extends BaseIntegrationTesting
{
    /**
     * Get the route name.
     *
     * @return string
     */
    private function getRouteName(): string
    {
        return 'authentication.login';
    }

    /**
     * Setup new test environments.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAsGuest();
    }

    /**
     * Test if can't login with invalid email.
     *
     * @return void
     */
    public function test_if_cant_login_with_invalid_email(): void
    {
        $this->postJson(route($this->getRouteName()), [
            'email' => 'i n v a l i d',
            'password' => 'admin1234',
        ])->assertUnprocessable()
            ->assertSee('O campo email deve ser um endere\u00e7o de e-mail v\u00e1lido.');
    }

    /**
     * Test if can throw unauthorized if login is invalid.
     *
     * @return void
     */
    public function test_if_can_throw_unauthorized_if_login_is_invalid(): void
    {
        $this->postJson(route($this->getRouteName()), [
            'email' => $this->user->email,
            'password' => 'invalid',
        ])->assertUnauthorized()->assertSee('N\u00e3o foi poss\u00edvel encontrar o usu\u00e1rio. Por favor, verifique os dados e tente novamente.');
    }

    /**
     * Can authenticate with email.
     *
     * @return void
     */
    public function test_if_can_authenticate_with_email(): void
    {
        $this->postJson(route($this->getRouteName()), [
            'email' => $this->user->email,
            'password' => 'password',
        ])->assertOk();
    }

    /**
     * Test if can get correct json structure.
     *
     * @return void
     */
    public function test_if_can_get_correct_json_structure(): void
    {
        $this->postJson(route($this->getRouteName()), [
            'email' => $this->user->email,
            'password' => 'password',
        ])->assertOk()->assertJsonStructure([
            'data' => [
                'message',
                'token',
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
        $this->postJson(route($this->getRouteName()), [
            'email' => $this->user->email,
            'password' => 'password',
        ])->assertOk()->assertJson([
            'data' => [
                'message' => 'Usuário autenticado com sucesso!',
            ],
        ]);
    }

    /**
     * Test if can access an auth route after login.
     *
     * @return void
     */
    public function test_if_can_access_an_auth_route_after_login(): void
    {
        $this->getJson(route('user.me'))->assertUnauthorized();

        $this->postJson(route($this->getRouteName()), [
            'email' => $this->user->email,
            'password' => 'password',
        ])->assertOk();

        $this->getJson(route('user.me'))->assertOk();
    }
}
