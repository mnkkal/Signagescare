<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SopChecklist extends Model
{
    use HasFactory;

    protected $fillable = [
        'sop_project_id',
        'stage',
        'section_title',
        'checklist_item',
        'status',
        'remarks',
        'verified_by_user_id',
        'verified_at',
    ];

    protected function casts(): array
    {
        return [
            'verified_at' => 'datetime',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(SopProject::class, 'sop_project_id');
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by_user_id');
    }
}
