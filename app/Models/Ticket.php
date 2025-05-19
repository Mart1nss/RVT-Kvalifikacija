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
    const STATUS_CLOSED = 'closed'; // Changed from STATUS_RESOLVED = 'resolved'

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

    // Removed the boot method that generated custom ticket_id

    // Set default status if not provided - this can be handled by database default or in controller
    // If you still need default status logic here, it should be adjusted.
    // For now, assuming database default or controller logic handles it.
}
