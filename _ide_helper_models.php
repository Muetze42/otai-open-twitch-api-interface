<?php

// @formatter:off
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * App\Models\Activity
 *
 * @property int $id
 * @property string|null $log_name
 * @property string $description
 * @property string|null $subject_type
 * @property int|null $subject_id
 * @property string|null $event
 * @property string|null $causer_type
 * @property int|null $causer_id
 * @property \Illuminate\Support\Collection|null $properties
 * @property string|null $batch_uuid
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $causer
 * @property-read \Illuminate\Support\Collection $changes
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $subject
 * @method static \Illuminate\Database\Eloquent\Builder|Activity causedBy(\Illuminate\Database\Eloquent\Model $causer)
 * @method static \Illuminate\Database\Eloquent\Builder|Activity forBatch(string $batchUuid)
 * @method static \Illuminate\Database\Eloquent\Builder|Activity forEvent(string $event)
 * @method static \Illuminate\Database\Eloquent\Builder|Activity forSubject(\Illuminate\Database\Eloquent\Model $subject)
 * @method static \Illuminate\Database\Eloquent\Builder|Activity hasBatch()
 * @method static \Illuminate\Database\Eloquent\Builder|Activity inLog(...$logNames)
 * @method static \Illuminate\Database\Eloquent\Builder|Activity newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Activity newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Activity query()
 * @method static \Illuminate\Database\Eloquent\Builder|Activity whereBatchUuid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Activity whereCauserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Activity whereCauserType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Activity whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Activity whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Activity whereEvent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Activity whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Activity whereLogName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Activity whereProperties($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Activity whereSubjectId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Activity whereSubjectType($value)
 */
	class Activity extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Api
 *
 * @property int $id
 * @property string $name
 * @property string $base_url
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Resource> $resources
 * @property-read int|null $resources_count
 * @method static \Illuminate\Database\Eloquent\Builder|Api newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Api newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Api onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Api query()
 * @method static \Illuminate\Database\Eloquent\Builder|Api whereBaseUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Api whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Api whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Api whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Api whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Api whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Api withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Api withoutTrashed()
 */
	class Api extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Endpoint
 *
 * @property int $id
 * @property int $resource_id
 * @property string $name
 * @property string $slug
 * @property string $description
 * @property string $instruction
 * @property string $authorization
 * @property string $route
 * @property string $method
 * @property array|null $request_body
 * @property array|null $request_query_parameters
 * @property array|null $response_body
 * @property array|null $response_codes
 * @property bool $user_access_tokens_auth
 * @property bool $app_access_token_auth
 * @property bool $active
 * @property int $batch
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Activity> $activities
 * @property-read int|null $activities_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Request> $requests
 * @property-read int|null $requests_count
 * @property-read \App\Models\Resource $resource
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Scope> $scopes
 * @property-read int|null $scopes_count
 * @method static \Illuminate\Database\Eloquent\Builder|Endpoint newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Endpoint newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Endpoint onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Endpoint query()
 * @method static \Illuminate\Database\Eloquent\Builder|Endpoint whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Endpoint whereAppAccessTokenAuth($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Endpoint whereAuthorization($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Endpoint whereBatch($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Endpoint whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Endpoint whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Endpoint whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Endpoint whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Endpoint whereInstruction($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Endpoint whereMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Endpoint whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Endpoint whereRequestBody($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Endpoint whereRequestQueryParameters($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Endpoint whereResourceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Endpoint whereResponseBody($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Endpoint whereResponseCodes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Endpoint whereRoute($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Endpoint whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Endpoint whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Endpoint whereUserAccessTokensAuth($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Endpoint withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Endpoint withoutTrashed()
 */
	class Endpoint extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EventSub
 *
 * @property int $id
 * @property string $name
 * @property string $title
 * @property string $version
 * @property string $description
 * @property string|null $instruction
 * @property string|null $authorization
 * @property array|null $request_body
 * @property int $batch
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Activity> $activities
 * @property-read int|null $activities_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Scope> $scopes
 * @property-read int|null $scopes_count
 * @method static \Illuminate\Database\Eloquent\Builder|EventSub newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventSub newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventSub onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventSub query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventSub whereAuthorization($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSub whereBatch($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSub whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSub whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSub whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSub whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSub whereInstruction($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSub whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSub whereRequestBody($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSub whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSub whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSub whereVersion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSub withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventSub withoutTrashed()
 */
	class EventSub extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Request
 *
 * @property int $id
 * @property int $user_id
 * @property int|null $endpoint_id
 * @property string $url
 * @property string|null $notice
 * @property array|null $request_body
 * @property array|null $response_body
 * @property int $response_code
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\Endpoint|null $endpoint
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|Request newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Request newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Request onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Request query()
 * @method static \Illuminate\Database\Eloquent\Builder|Request whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Request whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Request whereEndpointId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Request whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Request whereNotice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Request whereRequestBody($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Request whereResponseBody($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Request whereResponseCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Request whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Request whereUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Request whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Request withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Request withoutTrashed()
 */
	class Request extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Resource
 *
 * @property int $id
 * @property int $api_id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Activity> $activities
 * @property-read int|null $activities_count
 * @property-read \App\Models\Api $api
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Endpoint> $endpoints
 * @property-read int|null $endpoints_count
 * @method static \Illuminate\Database\Eloquent\Builder|Resource newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Resource newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Resource onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Resource query()
 * @method static \Illuminate\Database\Eloquent\Builder|Resource whereApiId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Resource whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Resource whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Resource whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Resource whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Resource whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Resource withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Resource withoutTrashed()
 */
	class Resource extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Scope
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property bool $chat_and_pup_sub
 * @property int $batch
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Activity> $activities
 * @property-read int|null $activities_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Endpoint> $endpoints
 * @property-read int|null $endpoints_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\EventSub> $eventSubs
 * @property-read int|null $event_subs_count
 * @method static \Illuminate\Database\Eloquent\Builder|Scope newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Scope newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Scope onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Scope query()
 * @method static \Illuminate\Database\Eloquent\Builder|Scope whereBatch($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Scope whereChatAndPupSub($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Scope whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Scope whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Scope whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Scope whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Scope whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Scope whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Scope withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Scope withoutTrashed()
 */
	class Scope extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\User
 *
 * @property int $id
 * @property string $name
 * @property string|null $login
 * @property string $avatar
 * @property string|null $broadcaster_type
 * @property mixed $token
 * @property mixed $refresh_token
 * @property array $scopes
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $expired_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Request> $requests
 * @property-read int|null $requests_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User whereAvatar($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereBroadcasterType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereExpiredAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereLogin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRefreshToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereScopes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
 */
	class User extends \Eloquent {}
}

