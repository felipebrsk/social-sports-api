<?php

namespace Tests\Unit\Events\Authentication;

use Mockery;
use Tests\TestCase;
use App\Models\User;
use Mockery\MockInterface;
use App\Events\Authentication\LoginSuccessful;

class LoginSuccessfulTest extends TestCase
{
    /**
     * Test if the event can successfully store and expose the user instance.
     *
     * @return void
     */
    public function test_if_event_receives_and_exposes_user_instance(): void
    {
        /** @var User&MockInterface $user */
        $user = Mockery::mock(User::class);

        $event = new LoginSuccessful($user);

        $this->assertInstanceOf(LoginSuccessful::class, $event);
        $this->assertSame($user, $event->user);
    }
}
