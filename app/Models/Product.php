<?php

namespace App\Models;

use App\Models\Category;
use App\Models\Favorite;
use App\Models\Review;
use App\Models\ReadLater;
use App\Models\Note;
use App\Models\ReadBook;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'author',
        'category_id',
        'cover_image',
        'file'
    ];

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function readLater()
    {
        return $this->hasMany(ReadLater::class);
    }

    public function isInReadLaterOf($user)
    {
        if (!$user) {
            return false;
        }
        return $this->readLater()->where('user_id', $user->id)->exists();
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function isFavoritedBy($user = null)
    {
        if (!$user) {
            return false;
        }

        return $this->favorites()->where('user_id', $user->id)->exists();
    }

    public function notes()
    {
        return $this->hasMany(Note::class);
    }

    public function readBooks()
    {
        return $this->hasMany(ReadBook::class);
    }

    /**
     * Check if this book has been read by a specific user.
     *
     * @param User|null $user
     * @return bool
     */
    public function isReadBy($user = null)
    {
        if (!$user) {
            return false;
        }
        
        return $this->readBooks()->where('user_id', $user->id)->exists();
    }

    /**
     * Get the average rating for the product.
     *
     * @return float
     */
    public function getAverageRatingAttribute()
    {
        return $this->reviews()->avg('review_score');
    }
}
