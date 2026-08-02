<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * @property int $id
 * @property int $conversation_type_id
 * @property int|null $game_session_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\GameSession|null $gameSession
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Message> $messages
 * @property-read int|null $messages_count
 * @property-read \App\Models\ConversationType $type
 * @property-read \App\Models\ConversationUser|null $pivot
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @method static \Database\Factories\ConversationFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Conversation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Conversation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Conversation query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Conversation whereConversationTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Conversation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Conversation whereGameSessionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Conversation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Conversation whereUpdatedAt($value)
 */
	class Conversation extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Conversation> $conversations
 * @property-read int|null $conversations_count
 * @method static \Database\Factories\ConversationTypeFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConversationType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConversationType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConversationType query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConversationType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConversationType whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConversationType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConversationType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConversationType whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConversationType whereUpdatedAt($value)
 */
	class ConversationType extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string|null $last_message
 * @property \Illuminate\Support\Carbon|null $last_read_at
 * @property int $user_id
 * @property int $conversation_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Conversation $conversation
 * @property-read \App\Models\User $user
 * @method static \Database\Factories\ConversationUserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConversationUser newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConversationUser newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConversationUser query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConversationUser whereConversationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConversationUser whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConversationUser whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConversationUser whereLastMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConversationUser whereLastReadAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConversationUser whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConversationUser whereUserId($value)
 */
	class ConversationUser extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $title
 * @property string $description
 * @property string|null $admin_notes
 * @property string|null $feedbackable_type
 * @property int|null $feedbackable_id
 * @property int $feedback_status_id
 * @property int $feedback_category_id
 * @property int $user_id
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\FeedbackCategory $category
 * @property-read \App\Models\FeedbackStatus $status
 * @property-read \App\Models\User $user
 * @method static \Database\Factories\FeedbackFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Feedback newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Feedback newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Feedback onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Feedback query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Feedback whereAdminNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Feedback whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Feedback whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Feedback whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Feedback whereFeedbackCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Feedback whereFeedbackStatusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Feedback whereFeedbackableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Feedback whereFeedbackableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Feedback whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Feedback whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Feedback whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Feedback whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Feedback withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Feedback withoutTrashed()
 */
	class Feedback extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Feedback> $feedbacks
 * @property-read int|null $feedbacks_count
 * @method static \Database\Factories\FeedbackCategoryFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeedbackCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeedbackCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeedbackCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeedbackCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeedbackCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeedbackCategory whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeedbackCategory whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeedbackCategory whereUpdatedAt($value)
 */
	class FeedbackCategory extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Feedback> $feedbacks
 * @property-read int|null $feedbacks_count
 * @method static \Database\Factories\FeedbackStatusFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeedbackStatus newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeedbackStatus newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeedbackStatus query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeedbackStatus whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeedbackStatus whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeedbackStatus whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeedbackStatus whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FeedbackStatus whereUpdatedAt($value)
 */
	class FeedbackStatus extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property \Illuminate\Support\Carbon $start_time
 * @property int $max_players
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $end_time
 * @property bool $featured
 * @property int $external_players_count
 * @property int $venue_id
 * @property int $sport_id
 * @property int $game_session_status_id
 * @property int $creator_id
 * @property int|null $skill_level_id
 * @property int|null $host_team_id
 * @property int|null $visitor_team_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Conversation> $conversations
 * @property-read int|null $conversations_count
 * @property-read \App\Models\User $creator
 * @property-read \App\Models\Team|null $hostTeam
 * @property-read \App\Models\Payment|null $payment
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\GameSessionRequest> $requests
 * @property-read int|null $requests_count
 * @property-read \App\Models\SkillLevel|null $skillLevel
 * @property-read \App\Models\Sport $sport
 * @property-read \App\Models\GameSessionStatus $status
 * @property-read \App\Models\Venue $venue
 * @property-read \App\Models\Team|null $visitorTeam
 * @method static \Database\Factories\GameSessionFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GameSession newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GameSession newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GameSession query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GameSession whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GameSession whereCreatorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GameSession whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GameSession whereEndTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GameSession whereExternalPlayersCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GameSession whereFeatured($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GameSession whereGameSessionStatusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GameSession whereHostTeamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GameSession whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GameSession whereMaxPlayers($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GameSession whereSkillLevelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GameSession whereSportId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GameSession whereStartTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GameSession whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GameSession whereVenueId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GameSession whereVisitorTeamId($value)
 */
	class GameSession extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string|null $rejection_reason
 * @property int $user_id
 * @property int $game_session_request_status_id
 * @property int $game_session_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\GameSession $gameSession
 * @property-read \App\Models\GameSessionRequestStatus $status
 * @property-read \App\Models\User $user
 * @method static \Database\Factories\GameSessionRequestFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GameSessionRequest newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GameSessionRequest newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GameSessionRequest query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GameSessionRequest whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GameSessionRequest whereGameSessionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GameSessionRequest whereGameSessionRequestStatusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GameSessionRequest whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GameSessionRequest whereRejectionReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GameSessionRequest whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GameSessionRequest whereUserId($value)
 */
	class GameSessionRequest extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\GameSessionRequest> $requests
 * @property-read int|null $requests_count
 * @method static \Database\Factories\GameSessionRequestStatusFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GameSessionRequestStatus newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GameSessionRequestStatus newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GameSessionRequestStatus query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GameSessionRequestStatus whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GameSessionRequestStatus whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GameSessionRequestStatus whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GameSessionRequestStatus whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GameSessionRequestStatus whereUpdatedAt($value)
 */
	class GameSessionRequestStatus extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\GameSession> $gameSessions
 * @property-read int|null $game_sessions_count
 * @method static \Database\Factories\GameSessionStatusFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GameSessionStatus newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GameSessionStatus newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GameSessionStatus query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GameSessionStatus whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GameSessionStatus whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GameSessionStatus whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GameSessionStatus whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GameSessionStatus whereUpdatedAt($value)
 */
	class GameSessionStatus extends \Eloquent {}
}

namespace App\Models{
/**
 * @property string $path
 * @property-read \Illuminate\Database\Eloquent\Model $mediable
 * @property-read \App\Models\MediaType|null $type
 * @method static \Database\Factories\MediaFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Media newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Media newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Media query()
 */
	class Media extends \Eloquent {}
}

namespace App\Models{
/**
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Media> $media
 * @property-read int|null $media_count
 * @method static \Database\Factories\MediaTypeFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaType query()
 */
	class MediaType extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $content
 * @property int $user_id
 * @property int $conversation_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Conversation $conversation
 * @property-read \App\Models\User $user
 * @method static \Database\Factories\MessageFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Message newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Message newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Message query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Message whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Message whereConversationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Message whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Message whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Message whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Message whereUserId($value)
 */
	class Message extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $gateway
 * @property int $amount
 * @property string|null $pix_qr_code
 * @property string|null $gateway_transaction_id
 * @property int $user_id
 * @property int $payment_type_id
 * @property int $payment_status_id
 * @property int|null $game_session_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\GameSession|null $gameSession
 * @property-read \App\Models\PaymentStatus $status
 * @property-read \App\Models\PaymentType $type
 * @property-read \App\Models\User $user
 * @method static \Database\Factories\PaymentFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereGameSessionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereGateway($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereGatewayTransactionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment wherePaymentStatusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment wherePaymentTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment wherePixQrCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereUserId($value)
 */
	class Payment extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Payment> $payments
 * @property-read int|null $payments_count
 * @method static \Database\Factories\PaymentStatusFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentStatus newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentStatus newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentStatus query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentStatus whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentStatus whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentStatus whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentStatus whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentStatus whereUpdatedAt($value)
 */
	class PaymentStatus extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Payment> $payments
 * @property-read int|null $payments_count
 * @method static \Database\Factories\PaymentTypeFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentType query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentType whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentType whereUpdatedAt($value)
 */
	class PaymentType extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string|null $bio
 * @property string|null $avatar
 * @property string|null $whatsapp
 * @property string|null $instagram
 * @property int $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Database\Factories\ProfileFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profile newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profile newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profile query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profile whereAvatar($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profile whereBio($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profile whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profile whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profile whereInstagram($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profile whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profile whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profile whereWhatsapp($value)
 */
	class Profile extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\TeamUser> $teamUsers
 * @property-read int|null $team_users_count
 * @property-read \App\Models\RoleUser|null $pivot
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\VenueManager> $venueManagers
 * @property-read int|null $venue_managers_count
 * @method static \Database\Factories\RoleFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereUpdatedAt($value)
 */
	class Role extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property int $role_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Role $role
 * @property-read \App\Models\User $user
 * @method static \Database\Factories\RoleUserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoleUser newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoleUser newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoleUser query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoleUser whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoleUser whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoleUser whereRoleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoleUser whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoleUser whereUserId($value)
 */
	class RoleUser extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\GameSession> $gameSessions
 * @property-read int|null $game_sessions_count
 * @method static \Database\Factories\SkillLevelFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SkillLevel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SkillLevel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SkillLevel query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SkillLevel whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SkillLevel whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SkillLevel whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SkillLevel whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SkillLevel whereUpdatedAt($value)
 */
	class SkillLevel extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $url
 * @property string $linkable_type
 * @property int $linkable_id
 * @property int $social_network_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Model $linkable
 * @property-read \App\Models\SocialNetwork $socialNetwork
 * @method static \Database\Factories\SocialLinkFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialLink newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialLink newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialLink query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialLink whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialLink whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialLink whereLinkableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialLink whereLinkableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialLink whereSocialNetworkId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialLink whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialLink whereUrl($value)
 */
	class SocialLink extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string|null $icon
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SocialLink> $socialLinks
 * @property-read int|null $social_links_count
 * @method static \Database\Factories\SocialNetworkFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialNetwork newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialNetwork newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialNetwork query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialNetwork whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialNetwork whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialNetwork whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialNetwork whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialNetwork whereUpdatedAt($value)
 */
	class SocialNetwork extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string|null $icon
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\GameSession> $gameSessions
 * @property-read int|null $game_sessions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Team> $teams
 * @property-read int|null $teams_count
 * @property-read \App\Models\VenueSport|null $pivot
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Venue> $venues
 * @property-read int|null $venues_count
 * @method static \Database\Factories\SportFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sport newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sport newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sport query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sport whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sport whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sport whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sport whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sport whereUpdatedAt($value)
 */
	class Sport extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string|null $logo
 * @property string|null $description
 * @property int $sport_id
 * @property int $leader_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\GameSession> $gameSessions
 * @property-read int|null $game_sessions_count
 * @property-read \App\Models\User $leader
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Media> $media
 * @property-read int|null $media_count
 * @property-read \App\Models\Sport $sport
 * @property-read \App\Models\TeamUser|null $pivot
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @method static \Database\Factories\TeamFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team whereLeaderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team whereLogo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team whereSportId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team whereUpdatedAt($value)
 */
	class Team extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $role_id
 * @property int $user_id
 * @property int $team_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Role $role
 * @property-read \App\Models\Team $team
 * @property-read \App\Models\User $user
 * @method static \Database\Factories\TeamUserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeamUser newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeamUser newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeamUser query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeamUser whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeamUser whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeamUser whereRoleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeamUser whereTeamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeamUser whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeamUser whereUserId($value)
 */
	class TeamUser extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property bool $blocked
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\VenueManager|\App\Models\TeamUser|\App\Models\RoleUser|\App\Models\ConversationUser|null $pivot
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Conversation> $conversations
 * @property-read int|null $conversations_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\GameSession> $createdGameSessions
 * @property-read int|null $created_game_sessions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Feedback> $feedbacks
 * @property-read int|null $feedbacks_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Team> $ledTeams
 * @property-read int|null $led_teams_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Message> $messages
 * @property-read int|null $messages_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Payment> $payments
 * @property-read int|null $payments_count
 * @property-read \App\Models\Profile|null $profile
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\GameSessionRequest> $requests
 * @property-read int|null $requests_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Role> $roles
 * @property-read int|null $roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Team> $teams
 * @property-read int|null $teams_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Venue> $venues
 * @property-read int|null $venues_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereBlocked($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 */
	class User extends \Eloquent implements \Tymon\JWTAuth\Contracts\JWTSubject, \Illuminate\Contracts\Auth\MustVerifyEmail {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $city
 * @property string $address
 * @property string $state
 * @property string|null $neighborhood
 * @property float $latitude
 * @property float $longitude
 * @property bool $verified
 * @property bool $featured
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\GameSession> $gameSessions
 * @property-read int|null $game_sessions_count
 * @property-read \App\Models\VenueSport|\App\Models\VenueManager|null $pivot
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $managers
 * @property-read int|null $managers_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Media> $media
 * @property-read int|null $media_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Sport> $sports
 * @property-read int|null $sports_count
 * @method static \Database\Factories\VenueFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Venue newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Venue newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Venue query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Venue whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Venue whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Venue whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Venue whereFeatured($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Venue whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Venue whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Venue whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Venue whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Venue whereNeighborhood($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Venue whereState($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Venue whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Venue whereVerified($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Venue withDistance(float $latitude, float $longitude)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Venue withinRadius(float $radiusKm, float $latitude, float $longitude)
 */
	class Venue extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $role_id
 * @property int $user_id
 * @property int $venue_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Role $role
 * @property-read \App\Models\User $user
 * @property-read \App\Models\Venue $venue
 * @method static \Database\Factories\VenueManagerFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VenueManager newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VenueManager newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VenueManager query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VenueManager whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VenueManager whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VenueManager whereRoleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VenueManager whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VenueManager whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VenueManager whereVenueId($value)
 */
	class VenueManager extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $sport_id
 * @property int $venue_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Sport $sport
 * @property-read \App\Models\Venue $venue
 * @method static \Database\Factories\VenueSportFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VenueSport newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VenueSport newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VenueSport query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VenueSport whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VenueSport whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VenueSport whereSportId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VenueSport whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VenueSport whereVenueId($value)
 */
	class VenueSport extends \Eloquent {}
}

