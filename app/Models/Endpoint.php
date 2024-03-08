<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use NormanHuth\HelpersLaravel\Traits\Spatie\LogsActivityTrait;

class Endpoint extends Model
{
    use SoftDeletes;
    use LogsActivityTrait;

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'batch',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'instruction',
        'authorization',
        'route',
        'method',
        'request_body',
        'request_query_parameters',
        'response_body',
        'response_codes',
        'user_access_tokens_auth',
        'app_access_token_auth',
        'active',
        'batch',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'request_body' => 'array',
        'request_query_parameters' => 'array',
        'response_body' => 'array',
        'response_codes' => 'array',
        'user_access_tokens_auth' => 'bool',
        'app_access_token_auth' => 'bool',
        'active' => 'bool',
        'batch' => 'int',
    ];

    /**
     * Perform any actions required after the model boots.
     *
     * @return void
     */
    public static function booted(): void
    {
        static::saving(function(self $endpoint) {
            $endpoint->slug = Str::slug($endpoint->name);
        });
    }

    /**
     * Get the resource that owns the endpoint.
     */
    public function resource(): BelongsTo
    {
        return $this->belongsTo(Resource::class);
    }

    /**
     * The scopes that belong to the endpoint.
     */
    public function scopes(): BelongsToMany
    {
        return $this->belongsToMany(Scope::class);
    }

    /**
     * Get the requests for the endpoint.
     */
    public function requests(): HasMany
    {
        return $this->hasMany(Request::class);
    }
}
