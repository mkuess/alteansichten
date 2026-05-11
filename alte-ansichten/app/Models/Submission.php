<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Submission extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'place_id',
        'municipality_id',
        'submitted_by_name',
        'submitted_by_email',
        'submitted_by_phone',
        'title',
        'message',
        'material_type',
        'source_note',
        'rights_confirmation',
        'rights_note',
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
            'rights_confirmation' => 'boolean',
            'reviewed_at'         => 'datetime',
        ];
    }

    public function place(): BelongsTo
    {
        return $this->belongsTo(Place::class);
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
