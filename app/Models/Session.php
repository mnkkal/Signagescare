<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Session extends Model
{
    public $timestamps = false;
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'user_id',
        'ip_address',
        'user_agent',
        'payload',
        'last_activity',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getLastActiveAttribute(): string
    {
        return Carbon::createFromTimestamp($this->last_activity)->diffForHumans();
    }
}
