<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TicketUnassignedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public User $adminUser;
    public int $ticketCount;
    public string $reason; // 'role_changed' or 'account_deleted'

    /**
     * Create a new notification instance.
     *
     * @param User $adminUser The admin user whose tickets were unassigned.
     * @param int $ticketCount The number of tickets unassigned.
     * @param string $reason The reason for unassignment ('role_changed' or 'account_deleted').
     */
    public function __construct(User $adminUser, int $ticketCount, string $reason)
    {
        $this->adminUser = $adminUser;
        $this->ticketCount = $ticketCount;
        $this->reason = $reason;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable): array
    {
        // We'll store this in the database to be displayed in the UI
        // You could add 'mail' here if you also want to send emails
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     * (Optional, if you add 'mail' to via method)
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable): MailMessage
    {
        $reasonText = $this->reason === 'role_changed' ? 'role change' : 'account deletion';
        $subject = "Tickets Unassigned: {$this->adminUser->name}";
        $greeting = "Hello {$notifiable->name},";
        $line1 = "This is to inform you that {$this->ticketCount} support ticket(s) previously assigned to {$this->adminUser->name} have been unassigned due to {$reasonText}.";
        $line2 = "These tickets are now open and require attention from an available admin.";
        
        return (new MailMessage)
                    ->subject($subject)
                    ->greeting($greeting)
                    ->line($line1)
                    ->line($line2)
                    ->action('View Tickets', url('/tickets'));
    }

    /**
     * Get the array representation of the notification.
     * (For database storage)
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable): array
    {
        $reasonText = $this->reason === 'role_changed' ? 'role change' : 'account deletion';
        $message = "{$this->ticketCount} ticket(s) from {$this->adminUser->name} are now unassigned due to {$reasonText}. They require attention.";
        
        return [
            'admin_user_id' => $this->adminUser->id,
            'admin_user_name' => $this->adminUser->name,
            'ticket_count' => $this->ticketCount,
            'reason' => $this->reason,
            'message' => $message,
            'link' => route('tickets.index'),
            'type' => 'ticket_unassigned_warning',
        ];
    }
}
