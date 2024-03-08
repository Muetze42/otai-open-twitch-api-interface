<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use NormanHuth\HelpersLaravel\Traits\Spatie\LogsActivityTrait;

class EventSub extends Model
{
    use LogsActivityTrait;
    use SoftDeletes;

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
        'title',
        'version',
        'description',
        'instruction',
        'authorization',
        'request_body',
        'batch',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'request_body' => 'array',
    ];

    /**
     * The scopes that belong to the EventSub.
     */
    public function scopes(): BelongsToMany
    {
        return $this->belongsToMany(Scope::class);
    }
}
