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
        'status',
        'assigned_admin_id',
        'resolved_by',
        'resolved_at'
    ];

    protected $casts = [
        'resolved_at' => 'datetime'
    ];

    protected $dates = [
        'resolved_at'
    ];

    const STATUS_OPEN = 'open';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_RESOLVED = 'resolved';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function assignedAdmin()
    {
        return $this->belongsTo(User::class, 'assigned_admin_id');
    }

    public function responses()
    {
        return $this->hasMany(TicketResponse::class);
    }

    public function resolved_by_user()
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($ticket) {
            // Get the latest ticket ID
            $latestTicket = static::orderBy('id', 'desc')->first();
            $nextId = $latestTicket ? intval(substr($latestTicket->ticket_id, 1)) + 1 : 1;
            $ticket->ticket_id = '#' . $nextId;
            
            // Set default status
            if (!$ticket->status) {
                $ticket->status = self::STATUS_OPEN;
            }
        });
    }
}
