<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Booking extends Model
{
    protected $fillable = [
        'user_id', 'property_id', 'start_date', 'end_date', 
        'total_price', 'status', 'payment_method', 'payment_card', 'note'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'total_price' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function review(): HasOne
    {
        return $this->hasOne(Review::class);
    }

    public function getDaysCount(): int
    {
        return $this->start_date->diffInDays($this->end_date);
    }
}
