<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Favorite;
use App\Models\Review;
use App\Models\Ticket;
use App\Models\Notification;
use App\Notifications\CustomResetPassword;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'last_online',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'last_online' => 'datetime',
    ];

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    // app/Models/User.php
    public function notes()
    {
        return $this->hasMany(Note::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function isAdmin()
    {
        return $this->usertype === 'admin';
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new CustomResetPassword($token));
    }
}
