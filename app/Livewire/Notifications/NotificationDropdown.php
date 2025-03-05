<?php

namespace App\Livewire\Notifications;

use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use App\Models\NotificationRead;

class NotificationDropdown extends Component
{
  use WithPagination;

  public $isOpen = false;
  public $loading = true;
  public $perPage = 10;
  public $hasMoreNotifications = false;
  public $notifications = [];

  /**
   * Toggle the notification dropdown
   */
  public function toggleDropdown()
  {
    $this->isOpen = !$this->isOpen;

    if ($this->isOpen) {
      $this->loading = true;
      // Reset pagination when opening
      $this->resetPage();

      // Load notifications asynchronously
      $this->dispatch('loadNotifications');

      // Automatically mark all notifications as read when opening the dropdown
      $this->markAllAsRead();
    }
  }

  /**
   * Load notifications asynchronously
   */
  #[On('loadNotifications')]
  public function loadNotifications()
  {
    $paginatedNotifications = auth()->user()->notifications()
      ->orderBy('created_at', 'desc')
      ->paginate($this->perPage);

    // Extract the data from the paginated collection
    $this->notifications = $paginatedNotifications->items();

    $this->hasMoreNotifications = $paginatedNotifications->hasMorePages();
    $this->loading = false;
  }

  /**
   * Close the notification dropdown
   */
  #[On('closeNotifications')]
  public function closeDropdown()
  {
    $this->isOpen = false;
  }

  /**
   * Load more notifications when scrolling
   */
  public function loadMore()
  {
    $this->perPage += 10;
    $this->loadNotifications();
  }

  /**
   * Mark all notifications as read
   */
  public function markAllAsRead()
  {
    $notifications = auth()->user()->unreadNotifications;

    foreach ($notifications as $notification) {
      if (isset($notification->data['sent_notification_id'])) {
        NotificationRead::firstOrCreate([
          'user_id' => auth()->id(),
          'sent_notification_id' => $notification->data['sent_notification_id'],
          'read_at' => now()
        ]);
      }
    }

    $notifications->markAsRead();

    // Emit event to update notification count
    $this->dispatch('notificationsRead');
  }

  /**
   * Mark a specific notification as read
   */
  public function markAsRead($notificationId)
  {
    $notification = auth()->user()->notifications()->findOrFail($notificationId);

    if (isset($notification->data['sent_notification_id'])) {
      NotificationRead::firstOrCreate([
        'user_id' => auth()->id(),
        'sent_notification_id' => $notification->data['sent_notification_id'],
        'read_at' => now()
      ]);
    }

    $notification->markAsRead();

    // Emit event to update notification count
    $this->dispatch('notificationsRead');
  }

  /**
   * Delete a specific notification
   */
  public function deleteNotification($notificationId)
  {
    $notification = auth()->user()->notifications()->findOrFail($notificationId);

    // Before deleting, check if we need to track the read status
    if ($notification->read_at && isset($notification->data['sent_notification_id'])) {
      try {
        // Check if the sent notification exists before creating the read record
        $sentNotificationExists = \App\Models\SentNotification::where('id', $notification->data['sent_notification_id'])->exists();

        if ($sentNotificationExists) {
          NotificationRead::firstOrCreate([
            'user_id' => auth()->id(),
            'sent_notification_id' => $notification->data['sent_notification_id'],
            'read_at' => $notification->read_at
          ]);
        }
      } catch (\Exception $e) {
        \Log::error('Error creating notification read record: ' . $e->getMessage());
      }
    }

    $notification->delete();

    // Remove the deleted notification from the local array
    $this->notifications = collect($this->notifications)
      ->filter(function ($item) use ($notificationId) {
        return $item->id !== $notificationId;
      })->values()->all();

    // Emit event to update notification count
    $this->dispatch('notificationsRead');
  }

  /**
   * Delete all notifications
   */
  public function deleteAllNotifications()
  {
    try {
      // Get all notifications with sent_notification_id before deleting
      $notifications = auth()->user()->notifications()->get();

      // Track read status for each notification with sent_notification_id
      foreach ($notifications as $notification) {
        if ($notification->read_at && isset($notification->data['sent_notification_id'])) {
          try {
            // Check if the sent notification exists before creating the read record
            $sentNotificationExists = \App\Models\SentNotification::where('id', $notification->data['sent_notification_id'])->exists();

            if ($sentNotificationExists) {
              NotificationRead::firstOrCreate([
                'user_id' => auth()->id(),
                'sent_notification_id' => $notification->data['sent_notification_id'],
                'read_at' => $notification->read_at
              ]);
            }
          } catch (\Exception $e) {
            \Log::error('Error creating notification read record: ' . $e->getMessage());
            // Continue with the next notification even if this one fails
          }
        }
      }

      // Delete all notifications
      auth()->user()->notifications()->delete();

      // Clear the local notifications array
      $this->notifications = [];

      // Emit event to update notification count
      $this->dispatch('notificationsRead');

    } catch (\Exception $e) {
      \Log::error('Error deleting all notifications: ' . $e->getMessage());
    }
  }

  /**
   * Listen for notification updates from other components
   */
  #[On('refreshNotifications')]
  public function refreshNotifications()
  {
    // If the dropdown is open, reload the notifications
    if ($this->isOpen) {
      $this->loadNotifications();
    }
  }

  /**
   * Render the component
   */
  public function render()
  {
    $unreadCount = auth()->user()->unreadNotifications()->count();

    return view('livewire.notifications.notification-dropdown', [
      'notifications' => $this->isOpen ? $this->notifications : [],
      'unreadCount' => $unreadCount
    ]);
  }
}