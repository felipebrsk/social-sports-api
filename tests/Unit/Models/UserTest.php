<?php

namespace Tests\Unit\Models;

use App\Models\User;
use App\Notifications\Authentication\QueuedVerifyEmail;
use App\Notifications\Authentication\QueuedResetPassword;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Notification;
use Tests\Contracts\Models\{
    ShouldTestCasts,
    ShouldTestTable,
    BaseModelTesting,
    ShouldTestTraits,
    ShouldTestGuarded,
    ShouldTestRelations,
    ShouldTestFillables,
    ShouldTestInterfaces,
};

class UserTest extends BaseModelTesting implements
    ShouldTestCasts,
    ShouldTestTable,
    ShouldTestTraits,
    ShouldTestGuarded,
    ShouldTestFillables,
    ShouldTestRelations,
    ShouldTestInterfaces
{
    /**
     * @inheritDoc
     */
    public function model(): string
    {
        return User::class;
    }

    /**
     * @inheritDoc
     */
    public function test_fillable_attributes(): void
    {
        $fillable = [
            'name',
            'email',
            'blocked',
            'password',
        ];

        $this->assertHasFillables($fillable);
    }

    /**
     * @inheritDoc
     */
    public function test_casts_attributes(): void
    {
        $casts = [
            'id' => 'int',
            'blocked' => 'bool',
            'password' => 'hashed',
            'email_verified_at' => 'datetime',
        ];

        $this->assertHasCasts($casts);
    }

    /**
     * @inheritDoc
     */
    public function test_relations_attributes(): void
    {
        $relations = [
            'profile' => HasOne::class,
            'requests' => HasMany::class,
            'messages' => HasMany::class,
            'payments' => HasMany::class,
            'ledTeams' => HasMany::class,
            'feedbacks' => HasMany::class,
            'roles' => BelongsToMany::class,
            'teams' => BelongsToMany::class,
            'venues' => BelongsToMany::class,
            'notifications' => MorphMany::class,
            'readNotifications' => MorphMany::class,
            'createdGameSessions' => HasMany::class,
            'conversations' => BelongsToMany::class,
            'unreadNotifications' => MorphMany::class,
        ];

        $this->assertHasRelations($relations);
    }

    /**
     * @inheritDoc
     */
    public function test_interfaces_attributes(): void
    {
        $interfaces = [
            JWTSubject::class,
            MustVerifyEmail::class,
        ];

        $this->assertUsesInterfaces($interfaces);
    }

    /**
     * @inheritDoc
     */
    public function test_guarded_attributes(): void
    {
        $guarded = [
            '*',
        ];

        $this->assertHasGuarded($guarded);
    }

    /**
     * @inheritDoc
     */
    public function test_table_attribute(): void
    {
        $table = 'users';

        $this->assertHasTable($table);
    }

    /**
     * @inheritDoc
     */
    public function test_traits_attributes(): void
    {
        $traits = [
            HasFactory::class,
            Notifiable::class,
        ];

        $this->assertUsesTraits($traits);
    }

    /**
     * Test if password reset notification is dispatched correctly.
     */
    public function test_send_password_reset_notification(): void
    {
        Notification::fake();

        /** @var User $user */
        $user = User::factory()->make();
        $token = 'reset-token-123';

        $user->sendPasswordResetNotification($token);

        Notification::assertSentTo(
            [$user],
            QueuedResetPassword::class,
            function (QueuedResetPassword $notification) use ($token) {
                return $notification->token === $token;
            },
        );
    }

    /**
     * Test if email verification notification is dispatched correctly.
     */
    public function test_send_email_verification_notification(): void
    {
        Notification::fake();

        /** @var User $user */
        $user = User::factory()->make();

        $user->sendEmailVerificationNotification();

        Notification::assertSentTo(
            [$user],
            QueuedVerifyEmail::class,
        );
    }
}
