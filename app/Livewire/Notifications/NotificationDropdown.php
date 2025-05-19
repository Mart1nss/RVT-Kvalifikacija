<?php

namespace App\Livewire\Notifications;

use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use App\Models\SentNotification;
use App\Models\Ticket;
use Illuminate\Support\Facades\Log; // Added
// Removed: use App\Models\NotificationRead;

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

    // Removed NotificationRead::firstOrCreate logic
    // The markAsRead method on the collection handles updating 'read_at' in the 'notifications' table

    $notifications->markAsRead();

    // Emit event to update notification count
    $this->dispatch('notificationsRead');
  }

  /**
   * Mark a specific notification as read
   */
  public function markAsRead($notificationId)
  {
    $notification = auth()->user()->notifications()->find($notificationId);

    // Removed NotificationRead::firstOrCreate logic
    // The markAsRead method handles updating 'read_at' in the 'notifications' table
    if ($notification) {
        $notification->markAsRead();
        // Emit event to update notification count
        $this->dispatch('notificationsRead');
    }
  }

  /**
   * View a specific notification and navigate if its target exists
   */
  public function viewNotification($userNotificationId)
  {
    Log::debug("viewNotification called for userNotificationId: {$userNotificationId}");
    $userNotification = auth()->user()->notifications()->find($userNotificationId);

    if (!$userNotification) {
      Log::warning("User notification not found for ID: {$userNotificationId}");
      $this->dispatch('showToast', message: 'Notification not found.', type: 'error');
      return;
    }

    Log::debug("User notification data: ", $userNotification->data);

    $targetLink = $userNotification->data['link'] ?? null;
    $ticketId = $userNotification->data['ticket_id'] ?? null;
    $sentNotificationId = $userNotification->data['sent_notification_id'] ?? null;

    Log::debug("TargetLink: {$targetLink}, TicketID: {$ticketId}, SentNotificationID: {$sentNotificationId}");

    if ($ticketId) {
      Log::debug("Handling ticket notification for ticket ID: {$ticketId}");
      $ticket = Ticket::find($ticketId);
      if (!$ticket) {
        Log::info("Ticket ID: {$ticketId} not found. Deleting user notification {$userNotificationId}.");
        $this->deleteNotification($userNotificationId); // Remove from user's list
        $this->dispatch('showToast', message: 'The associated ticket is no longer available.', type: 'info');
        return;
      }
      Log::debug("Ticket ID: {$ticketId} found. Marking as read and navigating.");
      $this->markAsRead($userNotificationId);
      $this->dispatch('navigateTo', url: route('tickets.show', $ticketId));
    } elseif ($sentNotificationId && $targetLink) {
      Log::debug("Handling sent notification for sentNotificationId: {$sentNotificationId}");
      $sentNotification = SentNotification::find($sentNotificationId);
      if (!$sentNotification) {
        Log::info("SentNotification ID: {$sentNotificationId} not found. Deleting user notification {$userNotificationId}.");
        $this->deleteNotification($userNotificationId); // Remove from user's list
        $this->dispatch('showToast', message: 'This notification is no longer available.', type: 'info');
        return;
      }
      Log::debug("SentNotification ID: {$sentNotificationId} found. Marking as read and navigating to: {$targetLink}");
      $this->markAsRead($userNotificationId);
      $this->dispatch('navigateTo', url: $targetLink);
    } elseif ($targetLink) {
      Log::debug("Handling generic link notification: {$targetLink}");
      // Generic link, attempt to navigate after marking as read
      // This might still lead to 404 if the target is gone and we can't verify it
      $this->markAsRead($userNotificationId);
      $this->dispatch('navigateTo', url: $targetLink);
    } else {
      Log::debug("No actionable link for userNotificationId: {$userNotificationId}. Marking as read.");
      // No actionable link, just mark as read if not already
      $this->markAsRead($userNotificationId);
      // Optionally, provide feedback if there's no link but user tried to "view" it
      // $this->dispatch('showToast', message: 'No further details for this notification.', type: 'info');
    }
  }

  /**
   * Delete a specific notification
   */
  public function deleteNotification($notificationId)
  {
    Log::debug("deleteNotification called for notificationId: {$notificationId}");
    $notification = auth()->user()->notifications()->find($notificationId); // Changed from findOrFail

    if ($notification) {
      Log::debug("Notification {$notificationId} found, deleting.");
      $notification->delete();

      // Remove the deleted notification from the local array
      $this->notifications = collect($this->notifications)
        ->filter(function ($item) use ($notificationId) {
          return $item->id !== $notificationId;
        })->values()->all();

      // Emit event to update notification count
      $this->dispatch('notificationsRead');
      Log::debug("Notification {$notificationId} deleted and list updated.");
    } else {
      Log::warning("Notification {$notificationId} not found for deletion.");
    }
  }

  /**
   * Delete all notifications
   */
  public function deleteAllNotifications()
  {
    try {
      // Removed NotificationRead::firstOrCreate logic
      // Deleting notifications from the 'notifications' table is sufficient

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

  #[On('refreshNotificationsGlobal')]
  public function handleGlobalNotificationRefresh()
  {
    // Always reload notifications if a sent one was deleted,
    // as it might affect the list even if the dropdown is closed (e.g. unread count)
    $this->loadNotifications();
    // Ensure unread count is also updated
    // $this->dispatch('notificationsRead'); // This might be redundant if loadNotifications implicitly updates counts for render
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
