<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReadBook extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'user_id',
        'product_id',
    ];
    
    protected $casts = [
    ];
    
    /**
     * Get the user who marked this book as read.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Get the book that was marked as read.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
