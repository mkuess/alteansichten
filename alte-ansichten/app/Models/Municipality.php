<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Municipality extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'district_id',
        'name',
        'slug',
        'summary',
        'description',
        'hero_image_path',
        'logo_path',
        'postal_code',
        'latitude',
        'longitude',
        'status',
        'public_profile_enabled',
        'internal_reference_code',
    ];

    protected function casts(): array
    {
        return [
            'public_profile_enabled' => 'boolean',
            'latitude'               => 'decimal:7',
            'longitude'              => 'decimal:7',
        ];
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }
}
