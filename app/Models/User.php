<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\UserFactory;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Notifications\Authentication\{
    QueuedVerifyEmail,
    QueuedResetPassword,
};
use Illuminate\Database\Eloquent\Attributes\{
    Hidden,
    Fillable,
};
use Illuminate\Database\Eloquent\Relations\{
    HasOne,
    HasMany,
    BelongsToMany,
};

#[Fillable([
    'name',
    'email',
    'blocked',
    'password',
    'email_verified_at',
])]
#[Hidden([
    'password',
    'remember_token',
])]
class User extends Authenticatable implements JWTSubject, MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory;

    use Notifiable;

    /**
     * {@inheritdoc}
     */
    public function getJWTIdentifier(): mixed
    {
        return $this->getKey();
    }

    /**
     * {@inheritdoc}
     *
     * @return array<string, mixed>
     */
    public function getJWTCustomClaims(): array
    {
        return [];
    }

    /**
     * Send the password reset notification.
     *
     * @param string $token
     * @return void
     */
    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new QueuedResetPassword($token));
    }

    /**
     * Send the email verification notification.
     *
     * @return void
     */
    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new QueuedVerifyEmail());
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'blocked' => 'bool',
            'password' => 'hashed',
            'email_verified_at' => 'datetime',
        ];
    }

    /**
     * Get the profile associated with the User
     *
     * @return HasOne<Profile, $this>
     */
    public function profile(): HasOne
    {
        return $this->hasOne(Profile::class);
    }

    /**
     * Get all of the requests for the User
     *
     * @return HasMany<GameSessionRequest, $this>
     */
    public function requests(): HasMany
    {
        return $this->hasMany(GameSessionRequest::class);
    }

    /**
     * Get all of the messages for the User
     *
     * @return HasMany<Message, $this>
     */
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    /**
     * Get all of the payments for the User
     *
     * @return HasMany<Payment, $this>
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Get all of the feedbacks for the User
     *
     * @return HasMany<Feedback, $this>
     */
    public function feedbacks(): HasMany
    {
        return $this->hasMany(Feedback::class);
    }

    /**
     * Game sessions created/organized by this user
     *
     * @return HasMany<GameSession, $this>
     */
    public function createdGameSessions(): HasMany
    {
        return $this->hasMany(GameSession::class, 'creator_id');
    }

    /**
     * Teams led/created by this user
     *
     * @return HasMany<Team, $this>
     */
    public function ledTeams(): HasMany
    {
        return $this->hasMany(Team::class, 'leader_id');
    }

    /**
     * The conversations that belong to the User
     *
     * @return BelongsToMany<Conversation, $this, ConversationUser>
     */
    public function conversations(): BelongsToMany
    {
        return $this->belongsToMany(Conversation::class, 'conversation_users')
            ->using(ConversationUser::class)
            ->withPivot(['last_message', 'last_read_at'])
            ->withTimestamps();
    }

    /**
     * The roles that belong to the User
     *
     * @return BelongsToMany<Role, $this, RoleUser>
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_users')
            ->using(RoleUser::class)
            ->withTimestamps();
    }

    /**
     * The teams that belong to the User
     *
     * @return BelongsToMany<Team, $this, TeamUser>
     */
    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class, 'team_users')
            ->using(TeamUser::class)
            ->withPivot(['role_id'])
            ->withTimestamps();
    }

    /**
     * The venues managed by the User
     *
     * @return BelongsToMany<Venue, $this, VenueManager>
     */
    public function venues(): BelongsToMany
    {
        return $this->belongsToMany(Venue::class, 'venue_managers')
            ->using(VenueManager::class)
            ->withPivot(['role_id'])
            ->withTimestamps();
    }
}
