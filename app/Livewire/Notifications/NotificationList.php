<?php

namespace App\Livewire\Notifications;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;
use App\Models\SentNotification;
use Illuminate\Support\Facades\DB;

class NotificationList extends Component
{
  use WithPagination;

  public $sortType = 'newest';
  public $recipientFilter = '';
  public $totalCount = 0;
  public $filteredCount = 0;
  public $currentNotificationId = null;
  public $showDeleteModal = false;

  /**
   * Initialize the component
   */
  public function mount()
  {
    $this->totalCount = SentNotification::count();
  }

  /**
   * Set up property listeners
   */
  protected function getListeners()
  {
    return [
      'notificationSent' => 'handleNotificationSent'
    ];
  }

  /**
   * Reset pagination when sortType changes
   */
  public function updatedSortType()
  {
    $this->resetPage();
  }

  /**
   * Reset pagination when recipientFilter changes
   */
  public function updatedRecipientFilter()
  {
    $this->resetPage();
  }

  /**
   * Clear all filters
   */
  public function clearFilters()
  {
    $this->recipientFilter = '';
    $this->sortType = 'newest';
    $this->resetPage();
  }

  /**
   * Open delete confirmation modal
   */
  public function openDeleteModal($id)
  {
    $this->currentNotificationId = $id;
    $this->showDeleteModal = true;
  }

  /**
   * Close delete confirmation modal
   */
  public function closeDeleteModal()
  {
    $this->showDeleteModal = false;
    $this->currentNotificationId = null;
  }

  /**
   * Delete a sent notification
   */
  public function deleteSentNotification()
  {
    if (!$this->currentNotificationId) {
      return;
    }

    try {
      $sentNotification = SentNotification::findOrFail($this->currentNotificationId);

      // Delete all related notifications
      DB::table('notifications')
        ->where('data->sent_notification_id', $this->currentNotificationId)
        ->delete();

      // Delete the sent notification
      $sentNotification->delete();

      // Update counts
      $this->totalCount = SentNotification::count();

      // Close modal
      $this->closeDeleteModal();

      // Show success message
      session()->flash('success', 'Notification deleted successfully!');

      // Dispatch event for JavaScript alert
      $this->dispatch('notificationDeleted');
      // Dispatch a global event for other components to refresh
      $this->dispatch('refreshNotificationsGlobal');

    } catch (\Exception $e) {
      \Log::error('Error deleting sent notification: ' . $e->getMessage());
      session()->flash('error', 'Failed to delete notification. Please try again.');
    }
  }

  /**
   * Listen for notification sent event
   */
  #[On('notificationSent')]
  public function handleNotificationSent()
  {
    // Update counts and refresh the list
    $this->totalCount = SentNotification::count();
  }

  /**
   * Render the component
   */
  public function render()
  {
    $query = SentNotification::with('sender');

    // Apply recipient filter
    if (!empty($this->recipientFilter)) {
      $query->where('recipient_type', $this->recipientFilter);
    }

    // Apply sorting
    if ($this->sortType === 'oldest') {
      $query->orderBy('created_at', 'asc');
    } else {
      $query->orderBy('created_at', 'desc');
    }

    $notifications = $query->paginate(15);
    $this->filteredCount = $notifications->total();

    return view('livewire.notifications.notification-list', [
      'notifications' => $notifications
    ]);
  }
}
