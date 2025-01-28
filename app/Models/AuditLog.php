<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
  protected $fillable = [
    'admin_id',
    'action',
    'action_type',
    'description',
    'affected_item_id',
    'affected_item_name'
  ];

  public function admin()
  {
    return $this->belongsTo(User::class, 'admin_id');
  }
}