<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Quotation extends Model
{
    protected $fillable = [
        'quotation_number', 'ticket_id', 'client_id', 'client_location_id', 'created_by',
        'status', 'subtotal', 'gst_amount', 'total_amount', 'notes', 'valid_until',
        'approved_by', 'approved_at', 'rejection_reason',
    ];

    protected function casts(): array
    {
        return [
            'valid_until' => 'date',
            'approved_at' => 'datetime',
            'subtotal' => 'decimal:2',
            'gst_amount' => 'decimal:2',
            'total_amount' => 'decimal:2',
        ];
    }

    public function ticket(): BelongsTo { return $this->belongsTo(Ticket::class); }
    public function client(): BelongsTo { return $this->belongsTo(Client::class); }
    public function location(): BelongsTo { return $this->belongsTo(ClientLocation::class, 'client_location_id'); }
    public function creator(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }
    public function approver(): BelongsTo { return $this->belongsTo(User::class, 'approved_by'); }
    public function items(): HasMany { return $this->hasMany(QuotationItem::class); }
}
