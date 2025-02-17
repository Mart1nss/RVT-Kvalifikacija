<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SentNotification extends Model
{
  use HasFactory;

  protected $fillable = ['sender_id', 'message', 'recipient_type'];

  protected $appends = ['read_count', 'total_users'];

  protected $with = ['sender']; // Always eager load sender

  public function sender()
  {
    return $this->belongsTo(User::class, 'sender_id');
  }

  public function reads()
  {
    return $this->hasMany(NotificationRead::class);
  }

  public function getReadCountAttribute()
  {
    return $this->reads()->count();
  }

  public function getTotalUsersAttribute()
  {
    try {
      $query = User::query();

      switch ($this->recipient_type) {
        case 'all':
          return User::count();
        case 'users':
          return User::where('usertype', '!=', 'admin')->count();
        case 'admins':
          return User::where('usertype', 'admin')->count();
        case 'self':
          return 1;
        default:
          return 0;
      }
    } catch (\Exception $e) {
      \Log::error('Error calculating total users: ' . $e->getMessage());
      return 0;
    }
  }
}