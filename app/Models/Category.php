<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Product;

class Category extends Model
{
    protected $fillable = [
        'name',
        'is_public',
        'is_system'
    ];

    protected $casts = [
        'is_public' => 'boolean',
        'is_system' => 'boolean'
    ];

    public function scopePublic(Builder $query): void
    {
        $query->where('is_public', true);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'category_id', 'id');
    }
}
