<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\Ticket;

class TicketNotification extends Notification
{
    use Queueable;

    private $ticket;
    private $type;
    private $message;

    /**
     * Create a new notification instance.
     */
    public function __construct(Ticket $ticket, string $type, string $message)
    {
        $this->ticket = $ticket;
        $this->type = $type;
        $this->message = $message;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'ticket_id' => $this->ticket->id,
            'type' => $this->type,
            'message' => $this->message,
            'link' => route('tickets.show', $this->ticket->id)
        ];
    }
}
