<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'avatar',
        'client_id',
        'status',
        'last_login_at',
        'last_login_ip',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function managedLocations(): HasMany
    {
        return $this->hasMany(ClientLocation::class, 'store_manager_user_id');
    }

    public function assignedTickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'assigned_technician_id');
    }

    public function reportedTickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'reported_by_user_id');
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(Session::class, 'user_id');
    }

    public function isSuperAdmin(): bool
    {
        return $this->hasRole('Super Admin');
    }

    public function isOperationsManager(): bool
    {
        return $this->hasRole('Operations Manager') || $this->hasRole('Super Admin');
    }

    public function isStoreManager(): bool
    {
        return $this->hasRole('Store Manager');
    }

    public function isTechnician(): bool
    {
        return $this->hasRole('Technician');
    }
}
