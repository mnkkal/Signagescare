<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SafetyChecklist extends Model
{
    protected $fillable = [
        'ticket_id', 'technician_id', 'ppe_verified', 'electrical_isolation_confirmed',
        'work_at_height_required', 'work_at_height_permit', 'permit_number', 'safety_notes', 'verified_at',
    ];

    protected function casts(): array
    {
        return [
            'ppe_verified' => 'boolean',
            'electrical_isolation_confirmed' => 'boolean',
            'work_at_height_required' => 'boolean',
            'work_at_height_permit' => 'boolean',
            'verified_at' => 'datetime',
        ];
    }

    public function ticket(): BelongsTo { return $this->belongsTo(Ticket::class); }
    public function technician(): BelongsTo { return $this->belongsTo(User::class, 'technician_id'); }
}
