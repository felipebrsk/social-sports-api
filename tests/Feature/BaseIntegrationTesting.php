<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Tests\Traits\Dummy\HasDummyUser;
use Illuminate\Foundation\Testing\RefreshDatabase;

abstract class BaseIntegrationTesting extends TestCase
{
    use HasDummyUser;
    use RefreshDatabase;

    /**
     * The dummy user.
     *
     * @var User
     */
    protected User $user;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->user = $this->actingAsDummyUser([
            'email' => 'valid@gmail.com',
        ]);
    }
}
