<?php

namespace App\Notifications\Authentication;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\{
    URL,
    Config,
};

use function call_user_func;

class QueuedVerifyEmail extends VerifyEmail implements ShouldQueue
{
    use Queueable;

    /**
     * Get the verification URL for the given notifiable.
     *
     * @param  mixed  $notifiable
     * @return string
     */
    protected function verificationUrl($notifiable)
    {
        /** @var User $notifiable */

        if (static::$createUrlCallback) {
            /** @var string */
            return call_user_func(static::$createUrlCallback, $notifiable);
        }

        /** @var int $expiration */
        $expiration = Config::get('auth.verification.expire', 60);

        return URL::temporarySignedRoute(
            'authentication.email.verify',
            Carbon::now()->addMinutes($expiration),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ],
        );
    }
}
