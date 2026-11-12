<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SparePart extends Model
{
    protected $fillable = ['sku', 'name', 'category', 'unit', 'current_stock', 'min_stock_alert', 'unit_cost', 'supplier_id'];

    protected function casts(): array
    {
        return ['unit_cost' => 'decimal:2'];
    }

    public function supplier(): BelongsTo { return $this->belongsTo(Supplier::class); }
    public function ticketParts(): HasMany { return $this->hasMany(TicketPart::class); }

    public function isLowStock(): bool
    {
        return $this->current_stock <= $this->min_stock_alert;
    }
}
