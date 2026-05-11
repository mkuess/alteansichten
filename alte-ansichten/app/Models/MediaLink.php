<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MediaLink extends Model
{
    protected $fillable = [
        'media_item_id',
        'linkable_type',
        'linkable_id',
        'relationship_type',
        'is_primary',
        'sort_order',
        'period_from',
        'period_to',
    ];

    protected function casts(): array
    {
        return [
            'is_primary'  => 'boolean',
            'sort_order'  => 'integer',
            'period_from' => 'integer',
            'period_to'   => 'integer',
        ];
    }

    public function mediaItem(): BelongsTo
    {
        return $this->belongsTo(MediaItem::class);
    }
}
