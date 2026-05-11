<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Place extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'municipality_id',
        'category_id',
        'title',
        'slug',
        'summary',
        'story',
        'street',
        'house_number',
        'postal_code',
        'address_text',
        'latitude',
        'longitude',
        'location_precision',
        'location_note',
        'period_from',
        'period_to',
        'approximate_date_text',
        'status',
        'visibility',
        'internal_reference_code',
    ];

    protected function casts(): array
    {
        return [
            'latitude'    => 'decimal:7',
            'longitude'   => 'decimal:7',
            'period_from' => 'integer',
            'period_to'   => 'integer',
        ];
    }

    public function municipality(): BelongsTo
    {
        return $this->belongsTo(Municipality::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
