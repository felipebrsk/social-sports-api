<?php

namespace Tests\Unit\Listeners;

use Mockery;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Events\Dispatcher;
use App\Events\Authentication\LoginSuccessful;
use App\Listeners\Authentication\AuthenticationSubscriber;

class AuthenticationSubscriberTest extends TestCase
{
    /**
     * Test if it maps events to the correct methods.
     *
     * @return void
     */
    public function test_if_it_maps_events_to_the_correct_methods(): void
    {
        $subscriber = new AuthenticationSubscriber();

        $dispatcher = Mockery::mock(Dispatcher::class);

        $events = $subscriber->subscribe($dispatcher);

        $this->assertArrayHasKey(LoginSuccessful::class, $events);
        $this->assertEquals('handleUserLogin', $events[LoginSuccessful::class]);
    }

    /**
     * Test if it can execute handle methods.
     *
     * @return void
     */
    public function test_if_it_can_execute_handle_methods(): void
    {
        $mockUser = new User();

        $loginEvent = new LoginSuccessful($mockUser);

        $subscriber = new AuthenticationSubscriber();

        $subscriber->handleUserLogin($loginEvent);

        $this->assertTrue(true); // @phpstan-ignore-line
    }
}
