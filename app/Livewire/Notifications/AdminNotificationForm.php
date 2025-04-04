<?php

namespace App\Livewire\Notifications;

use Livewire\Component;
use App\Models\User;
use App\Models\SentNotification;
use App\Notifications\AdminBroadcastNotification;
use App\Services\AuditLogService;

class AdminNotificationForm extends Component
{
  public $message = '';
  public $recipientType = 'all';
  public $isSending = false;

  /**
   * Validation rules
   */
  protected $rules = [
    'message' => 'required|max:250',
    'recipientType' => 'required|in:all,users,admins,self'
  ];

  /**
   * Send notification to selected recipients
   */
  public function sendNotification()
  {
    $this->validate();

    $this->isSending = true;

    try {
      // Store in sent notifications with recipient type
      $sentNotification = SentNotification::create([
        'sender_id' => auth()->id(),
        'message' => $this->message,
        'recipient_type' => $this->recipientType
      ]);

      // Get recipients based on type
      $users = $this->getRecipients($this->recipientType);

      // Send notifications to recipients
      foreach ($users as $user) {
        $user->notify(new AdminBroadcastNotification($this->message, $sentNotification->id));
      }

      // Log the action
      AuditLogService::log(
        "Sent notification",
        "notification",
        $this->message,
        null,
        "Notification to " . $this->recipientType
      );

      // Reset form
      $this->reset('message');

      // Show success message
      session()->flash('success', 'Notification sent successfully!');

      // Emit event to refresh notification list
      $this->dispatch('notificationSent');

      // Dispatch event for JavaScript alert
      $this->dispatch('notificationSent');

    } catch (\Exception $e) {
      // Log error
      \Log::error('Error sending notification: ' . $e->getMessage());

      // Show error message
      session()->flash('error', 'Failed to send notification. Please try again.');
    }

    $this->isSending = false;
  }

  /**
   * Get recipients based on type
   */
  private function getRecipients($type)
  {
    switch ($type) {
      case 'all':
        return User::all();
      case 'users':
        return User::where('usertype', '!=', 'admin')->get();
      case 'admins':
        return User::where('usertype', 'admin')->get();
      case 'self':
        return User::where('id', auth()->id())->get();
      default:
        return collect();
    }
  }

  /**
   * Render the component
   */
  public function render()
  {
    return view('livewire.notifications.admin-notification-form');
  }
}