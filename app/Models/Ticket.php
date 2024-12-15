<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\TicketResponse;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_id',
        'user_id',
        'title',
        'category',
        'description',
        'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function responses()
    {
        return $this->hasMany(TicketResponse::class);
    }

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($ticket) {
            $ticket->ticket_id = 'TKT-' . strtoupper(uniqid());
        });
    }
}
