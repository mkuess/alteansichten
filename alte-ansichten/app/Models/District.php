<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class District extends Model
{
    protected $fillable = [
        'state_id',
        'name',
        'slug',
        'code',
        'status',
    ];

    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }
}
