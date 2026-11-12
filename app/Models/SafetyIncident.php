<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SafetyIncident extends Model
{
    protected $fillable = [
        'ticket_id', 'client_location_id', 'reported_by', 'incident_type',
        'severity', 'description', 'corrective_action', 'incident_date', 'status',
    ];

    protected function casts(): array
    {
        return ['incident_date' => 'date'];
    }

    public function ticket(): BelongsTo { return $this->belongsTo(Ticket::class); }
    public function location(): BelongsTo { return $this->belongsTo(ClientLocation::class, 'client_location_id'); }
    public function reporter(): BelongsTo { return $this->belongsTo(User::class, 'reported_by'); }
}
