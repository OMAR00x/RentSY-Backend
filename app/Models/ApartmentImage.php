<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApartmentImage extends Model
{
    protected $fillable = ['apartment_id', 'url', 'is_main', 'order'];

    protected $casts = [
        'is_main' => 'boolean',
    ];

    public function apartment(): BelongsTo
    {
        return $this->belongsTo(apartment::class);
    }
}
