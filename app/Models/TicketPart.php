<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketPart extends Model
{
    protected $fillable = ['ticket_id', 'spare_part_id', 'quantity', 'unit_cost', 'total_cost', 'recorded_by'];

    protected function casts(): array
    {
        return [
            'unit_cost' => 'decimal:2',
            'total_cost' => 'decimal:2',
        ];
    }

    public function ticket(): BelongsTo { return $this->belongsTo(Ticket::class); }
    public function sparePart(): BelongsTo { return $this->belongsTo(SparePart::class); }
    public function recorder(): BelongsTo { return $this->belongsTo(User::class, 'recorded_by'); }
}
