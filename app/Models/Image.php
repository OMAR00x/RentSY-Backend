<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Image extends Model
{
    protected $fillable = ['url', 'type', 'is_main', 'order'];

    protected $casts = [
        'is_main' => 'boolean',
    ];

    protected function getUrlAttribute($value)
    {
        return asset('storage/' . $value);
    }

    public function imageable(): MorphTo
    {
        return $this->morphTo();
    }
}
