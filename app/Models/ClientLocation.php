<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClientLocation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'client_id',
        'name',
        'store_code',
        'city',
        'state',
        'address',
        'pincode',
        'latitude',
        'longitude',
        'store_manager_user_id',
        'primary_contact_number',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function storeManager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'store_manager_user_id');
    }

    public function assets(): HasMany
    {
        return $this->hasMany(SignageAsset::class);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    public function maintenanceSchedules(): HasMany
    {
        return $this->hasMany(MaintenanceSchedule::class);
    }

    public function amcContracts(): BelongsToMany
    {
        return $this->belongsToMany(AmcContract::class, 'amc_contract_locations');
    }
}
