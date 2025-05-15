<?php

namespace App\Listeners;

use App\Events\AdminTicketsUnassigned;
use App\Models\User;
use App\Notifications\TicketUnassignedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Log; // Added for logging

class SendTicketUnassignedNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  AdminTicketsUnassigned  $event
     * @return void
     */
    public function handle(AdminTicketsUnassigned $event): void
    {
        Log::info("[Listener SendTicketUnassignedNotification] Handling AdminTicketsUnassigned event for user ID {$event->adminUser->id} ({$event->adminUser->name}). Reason: {$event->reason}. Ticket count: {$event->ticketCount}.");

        // Only proceed if there are actual tickets unassigned
        if ($event->ticketCount > 0) {
            // Find all admin users except the one whose tickets were unassigned
            $otherAdmins = User::where('usertype', 'admin')
                                ->where('id', '!=', $event->adminUser->id)
                                ->get();

            if ($otherAdmins->isNotEmpty()) {
                Log::info("[Listener SendTicketUnassignedNotification] Found " . $otherAdmins->count() . " other admin(s) to notify for {$event->ticketCount} unassigned ticket(s).");
                // Send the notification to each of these admins
                Notification::send($otherAdmins, new TicketUnassignedNotification(
                    $event->adminUser,
                    $event->ticketCount,
                    $event->reason
                ));
                Log::info("[Listener SendTicketUnassignedNotification] TicketUnassignedNotification sent to other admins.");
            } else {
                Log::info("[Listener SendTicketUnassignedNotification] No other admins found to notify for user ID {$event->adminUser->id}.");
            }
        } else {
            Log::info("[Listener SendTicketUnassignedNotification] No tickets were unassigned for user ID {$event->adminUser->id} (count is {$event->ticketCount}). Notification not sent.");
        }
    }
}
