<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContentReport extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'place_id',
        'media_item_id',
        'municipality_id',
        'reporter_name',
        'reporter_email',
        'report_type',
        'message',
        'rights_claim',
        'status',
        'review_note',
        'reviewed_by_user_id',
        'reviewed_at',
        'ip_address',
        'user_agent',
    ];

    protected function casts(): array
    {
        return [
            'rights_claim' => 'boolean',
            'reviewed_at'  => 'datetime',
        ];
    }

    public function place(): BelongsTo
    {
        return $this->belongsTo(Place::class);
    }

    public function mediaItem(): BelongsTo
    {
        return $this->belongsTo(MediaItem::class);
    }

    public function municipality(): BelongsTo
    {
        return $this->belongsTo(Municipality::class);
    }

    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by_user_id');
    }
}
