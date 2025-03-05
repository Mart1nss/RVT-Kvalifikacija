<?php

namespace App\Livewire\Notifications;

use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Auth;

class NotificationCounter extends Component
{
  public $count = 0;

  /**
   * Initialize the component
   */
  public function mount()
  {
    $this->updateCount();
  }

  /**
   * Update the notification count
   */
  public function updateCount()
  {
    if (Auth::check()) {
      $this->count = Auth::user()->unreadNotifications()->count();
    }
  }

  /**
   * Listen for notification updates
   */
  #[On('notificationsRead')]
  public function handleNotificationsRead()
  {
    $this->updateCount();
  }

  /**
   * Render the component
   */
  public function render()
  {
    return view('livewire.notifications.notification-counter');
  }
}