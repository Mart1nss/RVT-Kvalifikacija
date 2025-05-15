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

  // Removed reads() relationship

  public function getReadCountAttribute()
  {
    // Count directly from the 'notifications' table
    // This assumes 'sent_notification_id' is stored in the 'data' JSON column of the 'notifications' table
    return \Illuminate\Support\Facades\DB::table('notifications')
      ->whereJsonContains('data->sent_notification_id', $this->id)
      ->whereNotNull('read_at')
      ->count();
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
