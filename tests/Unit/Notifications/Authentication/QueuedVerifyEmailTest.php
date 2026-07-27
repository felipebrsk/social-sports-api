<?php

namespace Tests\Unit\Notifications\Authentication;

use Mockery;
use Tests\TestCase;
use App\Models\User;
use Mockery\MockInterface;
use App\Notifications\Authentication\QueuedVerifyEmail;

class QueuedVerifyEmailTest extends TestCase
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
     * @var QueuedVerifyEmail $notification
     */
    private QueuedVerifyEmail $notification;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->notifiable = Mockery::mock(User::class);

        $this->notification = new QueuedVerifyEmail();
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
        $this->notifiable
            ->shouldReceive('getKey')
            ->once()
            ->andReturn(1);

        $this->notifiable
            ->shouldReceive('getEmailForVerification')
            ->once()
            ->andReturn('user@example.com');

        $mailMessage = $this->notification->toMail($this->notifiable);

        $this->assertSame('Verifique seu endereço de email', $mailMessage->subject);
        $this->assertContains(
            'Clique no botão abaixo para verificar seu endereço de e-mail.',
            $mailMessage->introLines
        );
        $this->assertContains(
            'Se você não solicitou nenhuma confirmação, favor desconsiderar este e-mail.',
            $mailMessage->outroLines
        );
        $this->assertSame('Verificar E-mail', $mailMessage->actionText);
        $this->assertNotEmpty($mailMessage->actionUrl);
    }
}
