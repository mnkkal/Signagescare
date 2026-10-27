<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class SopProject extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'project_code',
        'client_id',
        'client_location_id',
        'ticket_id',
        'title',
        'current_stage',
        'overall_status',
        'project_manager_id',
        'supervisor_id',
        'target_deadline',
        'completed_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'target_deadline' => 'date',
            'completed_at' => 'datetime',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(ClientLocation::class, 'client_location_id');
    }

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'project_manager_id');
    }

    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    public function checklists(): HasMany
    {
        return $this->hasMany(SopChecklist::class);
    }
}
