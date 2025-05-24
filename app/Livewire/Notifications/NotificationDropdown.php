<?php

namespace App\Livewire\Notifications;

use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use App\Models\SentNotification;
use App\Models\Ticket;

/**
 * Paziņojumu izvelkamā saraksta komponente
 * Nodrošina lietotāja paziņojumu attēlošanu, atzīmēšanu kā izlasītus un dzēšanu
 */
class NotificationDropdown extends Component
{
  use WithPagination;

  public $isOpen = false;
  public $loading = true;
  public $perPage = 10;
  public $hasMoreNotifications = false;
  public $notifications = [];

  /**
   * Pārslēdz paziņojumu izvelkamā saraksta stāvokli
   */
  public function toggleDropdown()
  {
    $this->isOpen = !$this->isOpen;

    if ($this->isOpen) {
      $this->loading = true;
      $this->resetPage();

      $this->dispatch('loadNotifications');

      $this->markAllAsRead();
    }
  }

  /**
   * Ielādē paziņojumus asinhroni
   */
  #[On('loadNotifications')]
  public function loadNotifications()
  {
    $paginatedNotifications = auth()->user()->notifications()
      ->orderBy('created_at', 'desc')
      ->paginate($this->perPage);

    $this->notifications = $paginatedNotifications->items();

    $this->hasMoreNotifications = $paginatedNotifications->hasMorePages();
    $this->loading = false;
  }

  /**
   * Aizver paziņojumu izvelkamo sarakstu
   */
  #[On('closeNotifications')]
  public function closeDropdown()
  {
    $this->isOpen = false;
  }

  /**
   * Ielādē vairāk paziņojumu, ritinot uz leju
   */
  public function loadMore()
  {
    $this->perPage += 10;
    $this->loadNotifications();
  }

  /**
   * Atzīmē visus paziņojumus kā izlasītus
   */
  public function markAllAsRead()
  {
    $notifications = auth()->user()->unreadNotifications;
    $notifications->markAsRead();

    $this->dispatch('notificationsRead');
  }

  /**
   * Atzīmē konkrētu paziņojumu kā izlasītu
   * @param string
   */
  public function markAsRead($notificationId)
  {
    $notification = auth()->user()->notifications()->find($notificationId);

    if ($notification) {
        $notification->markAsRead();
        $this->dispatch('notificationsRead');
    }
  }

  /**
   * Apskata konkrētu paziņojumu un pārvietojas uz tā mērķi, ja tas eksistē
   * @param string
   */
  public function viewNotification($userNotificationId)
  {
    $userNotification = auth()->user()->notifications()->find($userNotificationId);

    if (!$userNotification) {
      $this->dispatch('showToast', message: 'Notification not found.', type: 'error');
      return;
    }

    $targetLink = $userNotification->data['link'] ?? null;
    $ticketId = $userNotification->data['ticket_id'] ?? null;
    $sentNotificationId = $userNotification->data['sent_notification_id'] ?? null;

    if ($ticketId) {
      $ticket = Ticket::find($ticketId);
      if (!$ticket) {
        $this->deleteNotification($userNotificationId); // Noņem no lietotāja saraksta
        $this->dispatch('showToast', message: 'The associated ticket is no longer available.', type: 'info');
        return;
      }
      $this->markAsRead($userNotificationId);
      $this->dispatch('navigateTo', url: route('tickets.show', $ticketId));
    } elseif ($sentNotificationId && $targetLink) {
      $sentNotification = SentNotification::find($sentNotificationId);
      if (!$sentNotification) {
        $this->deleteNotification($userNotificationId); // Noņem no lietotāja saraksta
        $this->dispatch('showToast', message: 'This notification is no longer available.', type: 'info');
        return;
      }
      $this->markAsRead($userNotificationId);
      $this->dispatch('navigateTo', url: $targetLink);
    } elseif ($targetLink) {
      // Vispārīga saite, mēģina pārvietoties pēc atzīmēšanas kā izlasītu
      $this->markAsRead($userNotificationId);
      $this->dispatch('navigateTo', url: $targetLink);
    } else {
      // Nav darbojamas saites, vienkārši atzīmē kā izlasītu, ja vēl nav
      $this->markAsRead($userNotificationId);
    }
  }

  /**
   * Dzēš konkrētu paziņojumu
   * @param string
   */
  public function deleteNotification($notificationId)
  {
    $notification = auth()->user()->notifications()->find($notificationId);

    if ($notification) {
      $notification->delete();

      // Noņem dzēsto paziņojumu no lokālā masīva
      $this->notifications = collect($this->notifications)
        ->filter(function ($item) use ($notificationId) {
          return $item->id !== $notificationId;
        })->values()->all();

      $this->dispatch('notificationsRead');
    }
  }

  /**
   * Dzēš visus paziņojumus
   */
  public function deleteAllNotifications()
  {
    try {
      auth()->user()->notifications()->delete();

      $this->notifications = [];

      $this->dispatch('notificationsRead');

    } catch (\Exception $e) {
      \Log::error('Error deleting all notifications: ' . $e->getMessage());
    }
  }

  /**
   * Klausās paziņojumu atjauninājumus no citām komponentēm
   */
  #[On('refreshNotifications')]
  public function refreshNotifications()
  {
    if ($this->isOpen) {
      $this->loadNotifications();
    }
  }

  /**
   * Atjaunina paziņojumus pēc globāla notikuma
   */
  #[On('refreshNotificationsGlobal')]
  public function handleGlobalNotificationRefresh()
  {
    $this->loadNotifications();
  }

  /**
   * Renderē komponenti
   * @return \Illuminate\View\View
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
