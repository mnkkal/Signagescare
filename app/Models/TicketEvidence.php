<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketEvidence extends Model
{
    use HasFactory;

    protected $table = 'ticket_evidence';

    protected $fillable = [
        'ticket_id',
        'evidence_type',
        'file_path',
        'caption',
        'uploaded_by_user_id',
        'captured_at',
        'latitude',
        'longitude',
    ];

    protected function casts(): array
    {
        return [
            'captured_at' => 'datetime',
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
        ];
    }

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by_user_id');
    }
}
