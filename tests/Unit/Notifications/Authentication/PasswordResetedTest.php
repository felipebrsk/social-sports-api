<?php

namespace Tests\Unit\Notifications\Authentication;

use Mockery;
use Tests\TestCase;
use App\Models\User;
use Mockery\MockInterface;
use Illuminate\Support\Facades\Config;
use App\Notifications\Authentication\PasswordReseted;

class PasswordResetedTest extends TestCase
{
    /**
     * The notifiable.
     *
     * @var User&MockInterface
     */
    private User&MockInterface $notifiable;

    /**
     * The notification instance.
     *
     * @var PasswordReseted $notification
     */
    private PasswordReseted $notification;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->notifiable = Mockery::mock(User::class);

        $this->notification = new PasswordReseted();
    }

    /**
     * Test if the notification delivery channels are correct.
     *
     * @return void
     */
    public function test_if_notification_uses_correct_delivery_channels(): void
    {
        $channels = $this->notification->via($this->notifiable);

        $this->assertSame(['mail'], $channels);
    }

    /**
     * Test if the mail message representation is built correctly.
     *
     * @return void
     */
    public function test_if_mail_message_is_correctly_constructed(): void
    {
        $baseUrl = 'https://frontend.com';

        Config::set('app.frontend.base_url', $baseUrl);

        $this->notifiable
            ->shouldReceive('getAttribute')
            ->with('name')
            ->andReturn('John Doe');

        $mailMessage = $this->notification->toMail($this->notifiable);

        $this->assertSame('Olá, John Doe!', $mailMessage->greeting);
        $this->assertSame('Sua senha foi alterada com sucesso', $mailMessage->subject);

        $this->assertContains(
            'Estamos enviando este email para confirmar que a sua senha foi alterada com sucesso.',
            $mailMessage->introLines,
        );
        $this->assertContains(
            'Se não foi você que fez essa alteração, por favor, entre em contato com o suporte imediatamente.',
            $mailMessage->introLines,
        );

        $this->assertSame("{$baseUrl}/login", $mailMessage->actionUrl);
        $this->assertSame('Acessar minha conta', $mailMessage->actionText);
    }
}
