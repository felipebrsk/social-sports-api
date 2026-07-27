<?php

namespace App\Notifications\Authentication;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class PasswordReseted extends Notification implements ShouldQueue
{
    use Queueable;
    use InteractsWithQueue;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param object&User $notifiable
     * @return MailMessage
     */
    public function toMail(object $notifiable): MailMessage
    {
        /** @var string $baseUrl */
        $baseUrl = config('app.frontend.base_url');

        $baseUrl = rtrim($baseUrl);

        return (new MailMessage())
            ->subject('Sua senha foi alterada com sucesso')
            ->greeting("Olá, {$notifiable->name}!")
            ->line('Estamos enviando este email para confirmar que a sua senha foi alterada com sucesso.')
            ->line('Se não foi você que fez essa alteração, por favor, entre em contato com o suporte imediatamente.')
            ->action('Acessar minha conta', "$baseUrl/login");
    }
}
