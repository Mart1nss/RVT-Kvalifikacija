<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReadLater extends Model
{
  use HasFactory;

  /**
   * The table associated with the model.
   *
   * @var string
   */
  protected $table = 'read_later';

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = ['user_id', 'product_id'];

  public function user()
  {
    return $this->belongsTo(User::class);
  }

  public function product()
  {
    return $this->belongsTo(Product::class);
  }
}