<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class NotificationRead extends Model
{
  use HasFactory;

  protected $fillable = ['user_id', 'sent_notification_id', 'read_at'];

  protected $dates = ['read_at'];

  public function user()
  {
    return $this->belongsTo(User::class);
  }

  public function sentNotification()
  {
    return $this->belongsTo(SentNotification::class);
  }
}