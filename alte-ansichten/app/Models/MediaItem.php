<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class MediaItem extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'primary_place_id',
        'type',
        'title',
        'slug',
        'description',
        'file_path',
        'external_url',
        'year',
        'date_text',
        'source_note',
        'rights_note',
        'rights_status',
        'location_status',
        'location_note',
        'status',
        'internal_reference_code',
    ];

    protected function casts(): array
    {
        return [
            'year' => 'integer',
        ];
    }

    public function primaryPlace(): BelongsTo
    {
        return $this->belongsTo(Place::class, 'primary_place_id');
    }

    public function mediaLinks(): HasMany
    {
        return $this->hasMany(MediaLink::class);
    }

    public function contentReports(): HasMany
    {
        return $this->hasMany(ContentReport::class);
    }

    public function scopePubliclyVisible(Builder $query): Builder
    {
        return $query->where('status', 'approved')
                     ->whereNotNull('primary_place_id');
    }
}
