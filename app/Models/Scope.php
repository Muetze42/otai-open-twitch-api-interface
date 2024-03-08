<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use NormanHuth\HelpersLaravel\Traits\Spatie\LogsActivityTrait;

class Scope extends Model
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
        'chat_and_pup_sub',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'chat_and_pup_sub' => 'bool',
    ];

    /**
     * The endpoints that belong to the scope.
     */
    public function endpoints(): BelongsToMany
    {
        return $this->belongsToMany(Endpoint::class);
    }

    /**
     * The EventSub that belong to the scope.
     */
    public function eventSubs(): BelongsToMany
    {
        return $this->belongsToMany(EventSub::class);
    }
}
