<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class MaintenanceSchedule extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'client_location_id',
        'amc_contract_id',
        'assigned_technician_id',
        'scheduled_date',
        'last_visit_date',
        'next_visit_date',
        'status',
        'checklist_summary',
        'findings',
        'notes',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_date' => 'date',
            'last_visit_date' => 'date',
            'next_visit_date' => 'date',
            'completed_at' => 'datetime',
        ];
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(ClientLocation::class, 'client_location_id');
    }

    public function contract(): BelongsTo
    {
        return $this->belongsTo(AmcContract::class, 'amc_contract_id');
    }

    public function technician(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_technician_id');
    }
}
