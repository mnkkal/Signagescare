<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class SignageAsset extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'client_location_id',
        'asset_tag',
        'asset_name',
        'sign_type',
        'dimensions',
        'illumination_type',
        'installation_date',
        'warranty_expiry_date',
        'health_status',
        'primary_image_path',
        'specifications',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'installation_date' => 'date',
            'warranty_expiry_date' => 'date',
            'specifications' => 'array',
        ];
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(ClientLocation::class, 'client_location_id');
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    public function isHealthy(): bool
    {
        return $this->health_status === 'healthy';
    }

    public function needsAttention(): bool
    {
        return $this->health_status === 'attention';
    }

    public function isCritical(): bool
    {
        return $this->health_status === 'critical';
    }
}
