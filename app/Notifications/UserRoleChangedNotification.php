<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserRoleChangedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public User $affectedUser;
    public string $oldRole;
    public string $newRole;

    /**
     * Create a new notification instance.
     *
     * @param User $affectedUser The user whose role was changed.
     * @param string $oldRole The user's previous role.
     * @param string $newRole The user's new role.
     */
    public function __construct(User $affectedUser, string $oldRole, string $newRole)
    {
        $this->affectedUser = $affectedUser;
        $this->oldRole = ucfirst($oldRole); // Capitalize for display
        $this->newRole = ucfirst($newRole); // Capitalize for display
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable): array
    {
        return ['database']; // For display in the notification dropdown
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
        $message = "Your user role has been changed from '{$this->oldRole}' to '{$this->newRole}'.";
        
        return [
            'user_id' => $this->affectedUser->id,
            'old_role' => $this->oldRole,
            'new_role' => $this->newRole,
            'message' => $message,
            'link' => null, // No specific link for now, could be profile page: route('profile.show')
            'type' => 'user_role_changed', 
        ];
    }

    /**
     * Get the mail representation of the notification.
     * (Optional: if you want to email the user as well)
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage|null
     */
    public function toMail($notifiable): ?MailMessage
    {
        // You can enable this if you want to send an email too.
        // return (new MailMessage)
        //             ->subject('Your User Role Has Been Updated')
        //             ->greeting("Hello {$notifiable->name},")
        //             ->line("This is to inform you that your user role has been changed from '{$this->oldRole}' to '{$this->newRole}'.")
        //             ->lineIf($this->newRole === 'Admin', 'You now have administrator privileges.')
        //             ->lineIf($this->oldRole === 'Admin' && $this->newRole !== 'Admin', 'Your administrator privileges have been revoked.')
        //             ->action('View Your Profile', route('profile.show')); // Adjust route as needed
        return null;
    }
}
