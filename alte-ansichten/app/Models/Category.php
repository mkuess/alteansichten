<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'sort_order',
        'status',
    ];

    public function places(): HasMany
    {
        return $this->hasMany(Place::class);
    }
}
