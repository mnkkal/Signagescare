<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class AmcContract extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'contract_number',
        'client_id',
        'title',
        'start_date',
        'end_date',
        'visit_frequency',
        'sla_response_hours',
        'sla_resolution_hours',
        'status',
        'contract_value',
        'contract_doc_path',
        'coverage_scope',
        'terms_and_conditions',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'contract_value' => 'decimal:2',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function locations(): BelongsToMany
    {
        return $this->belongsToMany(ClientLocation::class, 'amc_contract_locations');
    }

    public function maintenanceSchedules(): HasMany
    {
        return $this->hasMany(MaintenanceSchedule::class);
    }
}
