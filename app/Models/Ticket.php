<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ticket extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'ticket_number',
        'client_location_id',
        'signage_asset_id',
        'mass_incident_id',
        'reported_by_user_id',
        'assigned_technician_id',
        'priority',
        'status',
        'category',
        'billing_type',
        'estimated_amount',
        'requires_client_approval',
        'client_approved_at',
        'client_approved_by',
        'problem_title',
        'problem_description',
        'eta_date',
        'eta_display',
        'scheduled_at',
        'first_response_at',
        'response_sla_minutes',
        'resolution_sla_minutes',
        'escalation_level',
        'escalated_at',
        'completed_at',
        'resolution_notes',
        'root_cause',
        'reopen_count',
        'reopen_reason',
        'reopened_at',
        'client_rating',
        'client_feedback',
        'rated_at',
    ];

    protected function casts(): array
    {
        return [
            'eta_date' => 'date',
            'scheduled_at' => 'datetime',
            'first_response_at' => 'datetime',
            'escalated_at' => 'datetime',
            'completed_at' => 'datetime',
            'client_approved_at' => 'datetime',
            'reopened_at' => 'datetime',
            'rated_at' => 'datetime',
            'requires_client_approval' => 'boolean',
            'estimated_amount' => 'decimal:2',
        ];
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(ClientLocation::class, 'client_location_id');
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(SignageAsset::class, 'signage_asset_id');
    }

    public function massIncident(): BelongsTo
    {
        return $this->belongsTo(MassIncident::class);
    }

    public function reportedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reported_by_user_id');
    }

    public function technician(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_technician_id');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_approved_by');
    }

    public function evidence(): HasMany
    {
        return $this->hasMany(TicketEvidence::class);
    }

    public function visits(): HasMany
    {
        return $this->hasMany(TicketVisit::class)->latest();
    }

    public function safetyChecklists(): HasMany
    {
        return $this->hasMany(SafetyChecklist::class)->latest();
    }

    public function quotations(): HasMany
    {
        return $this->hasMany(Quotation::class)->latest();
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class)->latest();
    }

    public function parts(): HasMany
    {
        return $this->hasMany(TicketPart::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(NotificationLog::class)->latest();
    }

    public function isSlaBreached(): bool
    {
        if (in_array($this->status, ['Completed', 'Cancelled'])) {
            return false;
        }
        $limit = $this->created_at?->copy()->addMinutes($this->resolution_sla_minutes ?? 1440);
        return $limit && now()->greaterThan($limit);
    }

    public function beforePhotos(): HasMany
    {
        return $this->hasMany(TicketEvidence::class)->where('evidence_type', 'before');
    }

    public function afterPhotos(): HasMany
    {
        return $this->hasMany(TicketEvidence::class)->where('evidence_type', 'after');
    }

    public function getPriorityBadgeColorAttribute(): string
    {
        return match ($this->priority) {
            'P1' => 'bg-rose-100 text-rose-700 border-rose-300 dark:bg-rose-950/40 dark:text-rose-400',
            'P2' => 'bg-amber-100 text-amber-700 border-amber-300 dark:bg-amber-950/40 dark:text-amber-400',
            'P3' => 'bg-blue-100 text-blue-700 border-blue-300 dark:bg-blue-950/40 dark:text-blue-400',
            'P4' => 'bg-slate-100 text-slate-700 border-slate-300 dark:bg-slate-800 dark:text-slate-400',
            default => 'bg-gray-100 text-gray-700',
        };
    }

    public function getStatusBadgeColorAttribute(): string
    {
        return match ($this->status) {
            'Open' => 'bg-sky-50 text-sky-700 border-sky-200',
            'Assigned' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
            'In Progress' => 'bg-amber-50 text-amber-700 border-amber-200',
            'Parts Required' => 'bg-orange-50 text-orange-700 border-orange-200',
            'Scheduled' => 'bg-purple-50 text-purple-700 border-purple-200',
            'Completed' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            'Overdue' => 'bg-red-50 text-red-700 border-red-200',
            default => 'bg-gray-50 text-gray-700 border-gray-200',
        };
    }
}
