<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    protected $fillable = ['name', 'contact_person', 'phone', 'email', 'gstin', 'address', 'category'];

    public function spareParts(): HasMany { return $this->hasMany(SparePart::class); }
}
