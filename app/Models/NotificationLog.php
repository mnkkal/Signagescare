<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationLog extends Model
{
    protected $fillable = [
        'ticket_id', 'amc_contract_id', 'channel', 'recipient',
        'event_type', 'message', 'status', 'provider_response',
    ];

    public function ticket(): BelongsTo { return $this->belongsTo(Ticket::class); }
    public function amcContract(): BelongsTo { return $this->belongsTo(AmcContract::class); }
}
