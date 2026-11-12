<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketVisit extends Model
{
    protected $fillable = [
        'ticket_id', 'technician_id', 'checked_in_at', 'checkin_lat', 'checkin_lng',
        'checked_out_at', 'checkout_lat', 'checkout_lng', 'travel_distance_km', 'vehicle_cost', 'visit_notes',
    ];

    protected function casts(): array
    {
        return [
            'checked_in_at' => 'datetime',
            'checked_out_at' => 'datetime',
            'travel_distance_km' => 'decimal:2',
            'vehicle_cost' => 'decimal:2',
        ];
    }

    public function ticket(): BelongsTo { return $this->belongsTo(Ticket::class); }
    public function technician(): BelongsTo { return $this->belongsTo(User::class, 'technician_id'); }
}
