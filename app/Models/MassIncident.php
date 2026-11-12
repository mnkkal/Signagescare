<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MassIncident extends Model
{
    protected $fillable = ['incident_code', 'title', 'incident_type', 'description', 'occurred_at', 'created_by', 'status'];

    protected function casts(): array
    {
        return ['occurred_at' => 'datetime'];
    }

    public function creator(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }
    public function tickets(): HasMany { return $this->hasMany(Ticket::class); }
}
