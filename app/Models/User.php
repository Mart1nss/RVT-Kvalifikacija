<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Favorite;
use App\Models\Review;
use App\Models\Ticket;
use App\Models\UserPreference;
use App\Models\Category;
use App\Models\Forum;
use App\Models\ForumReply;
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
        'last_read_book_id',
        'is_banned',
        'banned_at',
        'ban_reason',
        'banned_by'
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
        'banned_at' => 'datetime',
        'is_banned' => 'boolean',
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

    /**
     * Get all forums created by the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function forums()
    {
        return $this->hasMany(Forum::class);
    }

    /**
     * Get all forum replies created by the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function forumReplies()
    {
        return $this->hasMany(ForumReply::class);
    }

    public function isAdmin()
    {
        return $this->usertype === 'admin';
    }

    public function userPreferences()
    {
        return $this->hasMany(UserPreference::class);
    }

    public function preferredCategories()
    {
        return $this->belongsToMany(Category::class, 'user_preferences');
    }

    public function lastReadBook()
    {
        return $this->belongsTo(Product::class, 'last_read_book_id');
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new CustomResetPassword($token));
    }

    /**
     * Check if the user is banned.
     *
     * @return bool True if the user is banned, false otherwise
     */
    public function isBanned()
    {
        return $this->is_banned;
    }

    /**
     * Get the admin who banned this user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo Relationship to the admin who banned this user
     */
    public function bannedBy()
    {
        return $this->belongsTo(User::class, 'banned_by');
    }

    /**
     * Get all login records for the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function logins()
    {
        return $this->hasMany(UserLogin::class);
    }
}
