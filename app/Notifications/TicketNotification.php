<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\Ticket;
use App\Models\TicketResponse;
use App\Models\User;

class TicketNotification extends Notification
{
    use Queueable;

    private $ticket;
    private $type;
    private $message;
    private $data;

    // Notification types
    const TYPE_NEW_TICKET = 'new_ticket';
    const TYPE_STATUS_UPDATED = 'status_updated';
    const TYPE_NEW_RESPONSE = 'new_response';

    /**
     * Create a new notification instance.
     */
    public function __construct(Ticket $ticket, string $type, array $additionalData = [])
    {
        $this->ticket = $ticket;
        $this->type = $type;
        $this->data = $additionalData;
        $this->message = $this->generateMessage();
    }

    /**
     * Create a new ticket notification
     */
    public static function newTicket(Ticket $ticket): self
    {
        return new static($ticket, self::TYPE_NEW_TICKET);
    }

    /**
     * Create a status updated notification
     */
    public static function statusUpdated(Ticket $ticket): self
    {
        return new static($ticket, self::TYPE_STATUS_UPDATED);
    }

    /**
     * Create a new response notification
     */
    public static function newResponse(Ticket $ticket, TicketResponse $response): self
    {
        return new static($ticket, self::TYPE_NEW_RESPONSE, [
            'response_id' => $response->id,
            'responder' => $response->is_admin_response ? 'Admin' : $response->user->name
        ]);
    }

    /**
     * Generate the notification message based on type
     */
    private function generateMessage(): string
    {
        // If a custom message is provided, use it
        if (isset($this->data['message'])) {
            return $this->data['message'];
        }

        // Otherwise generate a message based on the type
        switch ($this->type) {
            case self::TYPE_NEW_TICKET:
                return "New support ticket (#{$this->ticket->id}) created by {$this->ticket->user->name}";

            case self::TYPE_STATUS_UPDATED:
                return "Your ticket (#{$this->ticket->id}) status has been updated to {$this->ticket->status}";

            case self::TYPE_NEW_RESPONSE:
                $responder = $this->data['responder'] ?? 'Someone';
                return "New response on ticket (#{$this->ticket->id}) from {$responder}";

            default:
                return "Update on ticket (#{$this->ticket->id})";
        }
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
        return array_merge([
            'ticket_id' => $this->ticket->id,
            'type' => $this->type,
            'message' => $this->message,
            'link' => route('tickets.show', $this->ticket->id)
        ], $this->data);
    }
}
