<?php

namespace App\Models;

use App\Models\Category;
use App\Models\Favorite;
use App\Models\Review;
use App\Models\ReadLater;

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
        'file',
        'is_public'
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
        return $this->belongsTo(Category::class);
    }

    public function isFavoritedBy($user = null)
    {
        if (!$user) {
            return false;
        }

        return $this->favorites()->where('user_id', $user->id)->exists();
    }
}
