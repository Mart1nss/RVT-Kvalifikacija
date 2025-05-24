<?php

namespace App\Livewire\Notifications;

use Livewire\Component;
use App\Models\User;
use App\Models\SentNotification;
use App\Notifications\AdminBroadcastNotification;
use App\Services\AuditLogService;

/**
 * Administratora paziņojumu formas komponente
 * Nodrošina iespēju administratoriem sūtīt paziņojumus lietotājiem
 */
class AdminNotificationForm extends Component
{
  public $message = '';
  public $recipientType = 'all';
  public $isSending = false;

  /**
   * Validācijas noteikumi
   */
  protected $rules = [
    'message' => 'required|max:250',
    'recipientType' => 'required|in:all,admins'
  ];

  /**
   * Nosūta paziņojumu izvēlētajiem saņēmējiem
   */
  public function sendNotification()
  {
    $this->validate();

    $this->isSending = true;

    $sentNotification = SentNotification::create([
      'sender_id' => auth()->id(),
      'message' => $this->message,
      'recipient_type' => $this->recipientType
    ]);

    $users = $this->getRecipients($this->recipientType);

    foreach ($users as $user) {
      $user->notify(new AdminBroadcastNotification($this->message, $sentNotification->id));
    }

    // Reģistrē darbību audita žurnālā
    AuditLogService::log(
      "Sent notification",
      "notification",
      $this->message,
      null,
      "Notification to " . $this->recipientType
    );

    $this->reset('message');

    session()->flash('success', 'Notification sent successfully!');

    // Nosūta notikumu, lai atjauninātu paziņojumu sarakstu
    $this->dispatch('notificationSent');

    // Nosūta notikumu JavaScript paziņojumam
    $this->dispatch('notificationSent');

    $this->isSending = false;
  }

  /**
   * Iegūst saņēmējus atkarībā no veida
   * @param string
   * @return \Illuminate\Database\Eloquent\Collection
   */
  private function getRecipients($type)
  {
    switch ($type) {
      case 'all':
        return User::all();
      case 'admins':
        return User::where('usertype', 'admin')->get();
      default:
        return collect();
    }
  }

  /**
   * Renderē komponenti
   * @return \Illuminate\View\View
   */
  public function render()
  {
    return view('livewire.notifications.admin-notification-form');
  }
}
