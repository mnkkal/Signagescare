<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'contact_person',
        'contact_email',
        'contact_phone',
        'logo_path',
        'is_active',
        'address',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function locations(): HasMany
    {
        return $this->hasMany(ClientLocation::class);
    }

    public function amcContracts(): HasMany
    {
        return $this->hasMany(AmcContract::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function sopProjects(): HasMany
    {
        return $this->hasMany(SopProject::class);
    }
}
