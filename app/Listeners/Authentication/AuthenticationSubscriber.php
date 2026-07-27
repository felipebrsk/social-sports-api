<?php

namespace App\Listeners\Authentication;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Events\Dispatcher;
use App\Events\Authentication\LoginSuccessful;

class AuthenticationSubscriber implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event subscriber instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle user login events.
     *
     * @param LoginSuccessful $event
     * @return void
     */
    public function handleUserLogin(LoginSuccessful $event): void
    {
        //
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @return array<class-string, string>
     */
    public function subscribe(Dispatcher $events): array
    {
        return [
            LoginSuccessful::class => 'handleUserLogin',
        ];
    }
}
