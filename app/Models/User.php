<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Favorite;
use App\Models\Review;
use App\Models\Ticket;
use App\Models\Notification;
use App\Models\UserPreference;
use App\Models\Category;
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
        'has_genre_preference_set',
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

    public function readLater()
    {
        return $this->hasMany(ReadLater::class);
    }

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

    public function userPreferences()
    {
        return $this->hasMany(UserPreference::class);
    }

    public function preferredCategories()
    {
        return $this->belongsToMany(Category::class, 'user_preferences');
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new CustomResetPassword($token));
    }
}
