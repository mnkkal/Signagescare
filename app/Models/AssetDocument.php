<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssetDocument extends Model
{
    protected $fillable = ['signage_asset_id', 'document_type', 'title', 'file_path', 'expiry_date', 'uploaded_by'];

    protected function casts(): array
    {
        return ['expiry_date' => 'date'];
    }

    public function asset(): BelongsTo { return $this->belongsTo(SignageAsset::class, 'signage_asset_id'); }
    public function uploader(): BelongsTo { return $this->belongsTo(User::class, 'uploaded_by'); }
}
