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
        'manufacturer_model',
        'power_rating_watts',
        'gps_coordinates',
        'sop_project_id',
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

    public function sopProject(): BelongsTo
    {
        return $this->belongsTo(SopProject::class, 'sop_project_id');
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(AssetDocument::class);
    }

    public function isRepeatFailure(): bool
    {
        return $this->tickets()
            ->where('created_at', '>=', now()->subMonths(6))
            ->count() >= 3;
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
