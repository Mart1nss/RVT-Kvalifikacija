<?php

namespace App\Notifications;

use App\Models\Ticket;
use App\Models\TicketResponse;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewTicketResponseNotification extends Notification
{
    use Queueable;

    protected $ticket;
    protected $response;

    /**
     * Create a new notification instance.
     */
    public function __construct(Ticket $ticket, TicketResponse $response)
    {
        $this->ticket = $ticket;
        $this->response = $response;
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
        $responder = $this->response->is_admin_response ? 'Admin' : $this->response->user->name;
        return [
            'message' => "New response on ticket ({$this->ticket->ticket_id}) from {$responder}",
            'ticket_id' => $this->ticket->id
        ];
    }
}
