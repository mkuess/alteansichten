<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class MediaItem extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'primary_place_id',
        'primary_municipality_id',
        'primary_district_id',
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

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (self $item) {
            // Auto-generate slug from title or year+type
            if (empty($item->slug)) {
                $base = $item->title
                    ? Str::slug($item->title)
                    : Str::slug(($item->year ?? 'media') . '-' . $item->type);
                $slug = $base;
                $i = 1;
                while (static::where('slug', $slug)->exists()) {
                    $slug = $base . '-' . $i++;
                }
                $item->slug = $slug;
            }

            // Auto-generate title if empty
            if (empty($item->title)) {
                $item->title = trim(implode(' ', array_filter([
                    $item->type ? ucfirst($item->type) : null,
                    $item->year ? '(' . $item->year . ')' : null,
                ]))) ?: 'Medium';
            }
        });
    }

    public function primaryPlace(): BelongsTo
    {
        return $this->belongsTo(Place::class, 'primary_place_id');
    }

    public function primaryMunicipality(): BelongsTo
    {
        return $this->belongsTo(Municipality::class, 'primary_municipality_id');
    }

    public function primaryDistrict(): BelongsTo
    {
        return $this->belongsTo(District::class, 'primary_district_id');
    }

    public function mediaLinks(): HasMany
    {
        return $this->hasMany(MediaLink::class);
    }

    // Readable primary context label for display
    public function getPrimaryContextLabelAttribute(): string
    {
        if ($this->primaryPlace) return $this->primaryPlace->title;
        if ($this->primaryMunicipality) return $this->primaryMunicipality->name;
        if ($this->primaryDistrict) return $this->primaryDistrict->name;
        return '—';
    }

    public function scopePubliclyVisible($query)
    {
        return $query->where('status', 'approved')
            ->where(function ($q) {
                $q->whereNotNull('primary_place_id')
                  ->orWhereNotNull('primary_municipality_id')
                  ->orWhereNotNull('primary_district_id')
                  ->orWhereHas('mediaLinks');
            });
    }
}
