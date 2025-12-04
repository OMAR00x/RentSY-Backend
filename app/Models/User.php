<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    protected $fillable = [
        'first_name',
        'last_name',
        'phone',
        'password',
        'role',
        'birthdate',
        'status'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'birthdate' => 'date',
        ];
    }

    public function apartments(): HasMany
    {
        return $this->hasMany(apartment::class, 'owner_id');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function sentMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'from_user_id');
    }

    public function receivedMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'to_user_id');
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    public function searchHistory(): HasMany
    {
        return $this->hasMany(SearchHistory::class);
    }

    public function images(): MorphMany
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    public function avatar(): MorphOne
    {
        return $this->morphOne(Image::class, 'imageable')->where('type', 'avatar');
    }

    public function idFront(): MorphOne
    {
        return $this->morphOne(Image::class, 'imageable')->where('type', 'id_front');
    }

    public function idBack(): MorphOne
    {
        return $this->morphOne(Image::class, 'imageable')->where('type', 'id_back');
    }

    public function isOwner(): bool
    {
        return $this->role === 'owner';
    }

    public function isRenter(): bool
    {
        return $this->role === 'renter';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }
}
