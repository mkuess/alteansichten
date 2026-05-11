<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QrCode extends Model
{
    protected $fillable = [
        'place_id',
        'code',
        'target_url',
        'png_path',
        'svg_path',
        'scan_count',
        'last_scanned_at',
    ];

    protected function casts(): array
    {
        return [
            'scan_count'     => 'integer',
            'last_scanned_at' => 'datetime',
        ];
    }

    public function place(): BelongsTo
    {
        return $this->belongsTo(Place::class);
    }
}
