<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AdminTicketsUnassigned
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public User $adminUser;
    public int $ticketCount;
    public string $reason; // 'role_changed' or 'account_deleted'

    /**
     * Create a new event instance.
     *
     * @param User $adminUser The admin user whose tickets were unassigned.
     * @param int $ticketCount The number of tickets unassigned.
     * @param string $reason The reason for unassignment.
     */
    public function __construct(User $adminUser, int $ticketCount, string $reason)
    {
        $this->adminUser = $adminUser;
        $this->ticketCount = $ticketCount;
        $this->reason = $reason;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [];
    }
}
