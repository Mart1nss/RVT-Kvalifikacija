<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;

class SentNotification extends Model
{
  use HasFactory;

  protected $fillable = ['sender_id', 'message'];

  public function sender()
  {
    return $this->belongsTo(User::class, 'sender_id');
  }

  public function getReadCountAttribute()
  {
    return DB::table('notifications')
      ->where('data->sent_notification_id', $this->id)
      ->whereNotNull('read_at')
      ->count();
  }

  public function getTotalUsersAttribute()
  {
    return User::count();
  }
}