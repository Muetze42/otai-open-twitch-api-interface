<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use NormanHuth\HelpersLaravel\Traits\Spatie\LogsActivityTrait;

class Resource extends Model
{
    use LogsActivityTrait;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name'
    ];

    /**
     * Get the endpoints for the resource.
     */
    public function endpoints(): HasMany
    {
        return $this->hasMany(Endpoint::class);
    }

    /**
     * Get the API that owns the resource.
     */
    public function api(): BelongsTo
    {
        return $this->belongsTo(Api::class);
    }
}
