<?php

namespace App\Events\Authentication;

use App\Models\User;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;

class LoginSuccessful
{
    use Dispatchable;
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @param User $user
     * @return void
     */
    public function __construct(
        public readonly User $user,
    ) {
        //
    }
}
