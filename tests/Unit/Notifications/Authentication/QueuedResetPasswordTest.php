<?php

namespace Tests\Unit\Notifications\Authentication;

use Mockery;
use Tests\TestCase;
use App\Models\User;
use Mockery\MockInterface;
use App\Notifications\Authentication\QueuedResetPassword;

class QueuedResetPasswordTest extends TestCase
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
     * @var QueuedResetPassword $notification
     */
    private QueuedResetPassword $notification;

    /**
     * The reset password token.
     *
     * @var string
     */
    private string $token = 'sample-reset-token-123';

    /**
     * Setup new test environments.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->notifiable = Mockery::mock(User::class);
        $this->notification = new QueuedResetPassword($this->token);
    }

    /**
     * Test if the notification delivery channels are correct.
     */
    public function test_if_notification_uses_correct_delivery_channels(): void
    {
        $channels = $this->notification->via($this->notifiable);

        $this->assertSame(['mail'], $channels);
    }

    /**
     * Test if the mail message representation is built correctly.
     */
    public function test_if_mail_message_is_correctly_constructed(): void
    {
        $this->notifiable
            ->shouldReceive('getEmailForPasswordReset')
            ->once()
            ->andReturn('user@example.com');

        $mailMessage = $this->notification->toMail($this->notifiable);

        $this->assertSame('Redefinir sua senha', $mailMessage->subject);
        $this->assertSame('Modificar Senha', $mailMessage->actionText);
        $this->assertStringContainsString($this->token, $mailMessage->actionUrl);

        $this->assertContains(
            'Você está recebendo este e-mail porque recebemos uma solicitação de redefinição de senha para sua conta.',
            $mailMessage->introLines,
        );

        $this->assertContains(
            'Se você não solicitou a redefinição de senha, nenhuma ação adicional será necessária.',
            $mailMessage->outroLines,
        );
    }
}
