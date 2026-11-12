<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    protected $fillable = [
        'invoice_number', 'ticket_id', 'quotation_id', 'amc_contract_id', 'client_id', 'client_location_id',
        'invoice_date', 'due_date', 'subtotal', 'cgst_amount', 'sgst_amount', 'igst_amount',
        'total_amount', 'paid_amount', 'balance_due', 'payment_status', 'gstin_client', 'notes', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'invoice_date' => 'date',
            'due_date' => 'date',
            'subtotal' => 'decimal:2',
            'cgst_amount' => 'decimal:2',
            'sgst_amount' => 'decimal:2',
            'igst_amount' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'paid_amount' => 'decimal:2',
            'balance_due' => 'decimal:2',
        ];
    }

    public function ticket(): BelongsTo { return $this->belongsTo(Ticket::class); }
    public function quotation(): BelongsTo { return $this->belongsTo(Quotation::class); }
    public function amcContract(): BelongsTo { return $this->belongsTo(AmcContract::class); }
    public function client(): BelongsTo { return $this->belongsTo(Client::class); }
    public function location(): BelongsTo { return $this->belongsTo(ClientLocation::class, 'client_location_id'); }
    public function creator(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }
    public function items(): HasMany { return $this->hasMany(InvoiceItem::class); }
    public function payments(): HasMany { return $this->hasMany(Payment::class)->latest('payment_date'); }

    public function recalculatePayments(): void
    {
        $paid = (float) $this->payments()->sum('amount');
        $total = (float) $this->total_amount;
        $balance = max(0, $total - $paid);

        $status = 'Unpaid';
        if ($paid >= $total && $total > 0) {
            $status = 'Paid';
        } elseif ($paid > 0) {
            $hasAdvance = $this->payments()->where('payment_type', 'Advance')->exists();
            $status = ($hasAdvance && $this->payments()->count() === 1) ? 'Advance' : 'Partial';
        }

        $this->update([
            'paid_amount' => $paid,
            'balance_due' => $balance,
            'payment_status' => $status,
        ]);
    }
}
